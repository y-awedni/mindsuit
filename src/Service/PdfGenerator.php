<?php

namespace App\Service;

use Mpdf\HTMLParserMode;
use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Renders a Twig template to a PDF with mPDF and returns it as an HTTP response.
 *
 * Server-side rendering gives byte-identical documents in every browser, unlike
 * the old window.print() approach which depended on each browser's print engine.
 *
 * When the template defines a `footer` block, that block is pinned to the bottom
 * of the LAST page (totals + signatures), while the `body` block flows naturally
 * across as many pages as the line items need. A template without a `footer`
 * block is rendered whole, top-to-bottom.
 *
 * The shared stylesheet (templates/pdf/_styles.css.twig) is loaded once per
 * document via HEADER_CSS so the body and the fixed-position footer share it.
 * It must NOT be inlined as a <style> tag inside the footer fragment: the path
 * WriteFixedPosHTML() takes does not strip <style>, so it would leak as text.
 */
class PdfGenerator
{
    private const STYLES = 'pdf/_styles.css.twig';

    private Environment $twig;
    private string $tempDir;

    public function __construct(Environment $twig, string $cacheDir)
    {
        $this->twig = $twig;
        // Under cacheDir so Symfony already keeps it writable for both CLI and
        // php-fpm. Mixing FPM/CLI runs under different users on a shared var/
        // subdir used to break mPDF's tempDir mkdir.
        $this->tempDir = $cacheDir . '/mpdf';
    }

    /**
     * Render $template with $context and return an inline-PDF response.
     * Inline disposition lets the browser preview it; the user can then print
     * or save. $filename is the suggested name when saving.
     */
    public function renderResponse(string $template, array $context, string $filename): Response
    {
        $tpl = $this->twig->load($template);

        if ($tpl->hasBlock('footer', $context)) {
            $body = $tpl->renderBlock('body', $context);
            $footer = $tpl->renderBlock('footer', $context);
            $pdf = $this->toPdfWithBottomFooter($body, $footer);
        } else {
            $pdf = $this->toPdf($this->twig->render($template, $context));
        }

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s.pdf"', $this->sanitize($filename)),
        ]);
    }

    private function newMpdf(): Mpdf
    {
        if (!is_dir($this->tempDir)) {
            @mkdir($this->tempDir, 0775, true);
        }

        return new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->tempDir,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
    }

    /** A fresh document with the shared stylesheet already registered. */
    private function newStyledMpdf(): Mpdf
    {
        $mpdf = $this->newMpdf();
        $mpdf->WriteHTML($this->twig->render(self::STYLES), HTMLParserMode::HEADER_CSS);

        return $mpdf;
    }

    private function toPdf(string $html): string
    {
        $mpdf = $this->newMpdf();
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    /**
     * Write $body normally (it may span several pages), then anchor $footer to
     * the bottom of whatever page the body ended on — adding one page first if
     * the footer would not fit in the space left below the body.
     */
    private function toPdfWithBottomFooter(string $body, string $footer): string
    {
        $mpdf = $this->newStyledMpdf();
        $mpdf->WriteHTML($body, HTMLParserMode::HTML_BODY);

        $footerHeight = $this->measureHeight($footer);
        $pageContentBottom = $mpdf->h - $mpdf->bMargin;

        // Not enough room under the body on this page → push the footer onto a
        // fresh page so it never overlaps the last line items.
        if (($pageContentBottom - $mpdf->y) < $footerHeight) {
            $mpdf->AddPage();
        }

        $contentWidth = $mpdf->w - $mpdf->lMargin - $mpdf->rMargin;
        $top = $pageContentBottom - $footerHeight;
        $mpdf->WriteFixedPosHTML($footer, $mpdf->lMargin, $top, $contentWidth, $footerHeight + 2, 'visible');

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    /**
     * Render $html in a throwaway document to learn how tall it is (in mm), so
     * the real document can bottom-align it precisely regardless of how many
     * VAT rows or how long the amount-in-words line turns out to be. The probe
     * shares the same stylesheet so its layout matches the real render.
     */
    private function measureHeight(string $html): float
    {
        $probe = $this->newStyledMpdf();
        $probe->WriteHTML($html, HTMLParserMode::HTML_BODY);

        return (float) ($probe->y - $probe->tMargin);
    }

    private function sanitize(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
    }
}
