<?php

namespace App\Service;

use Mpdf\Mpdf;
use Mpdf\Output\Destination;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Renders a Twig template to a PDF with mPDF and returns it as an HTTP response.
 *
 * Server-side rendering gives byte-identical documents in every browser, unlike
 * the old window.print() approach which depended on each browser's print engine.
 */
class PdfGenerator
{
    private Environment $twig;
    private string $tempDir;

    public function __construct(Environment $twig, string $projectDir)
    {
        $this->twig = $twig;
        $this->tempDir = $projectDir . '/var/mpdf';
    }

    /**
     * Render $template with $context and return an inline-PDF response.
     * Inline disposition lets the browser preview it; the user can then print
     * or save. $filename is the suggested name when saving.
     */
    public function renderResponse(string $template, array $context, string $filename): Response
    {
        $html = $this->twig->render($template, $context);
        $pdf = $this->toPdf($html);

        return new Response($pdf, Response::HTTP_OK, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => sprintf('inline; filename="%s.pdf"', $this->sanitize($filename)),
        ]);
    }

    private function toPdf(string $html): string
    {
        if (!is_dir($this->tempDir)) {
            @mkdir($this->tempDir, 0775, true);
        }

        $mpdf = new Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $this->tempDir,
            'margin_top' => 10,
            'margin_bottom' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
        ]);
        $mpdf->WriteHTML($html);

        return $mpdf->Output('', Destination::STRING_RETURN);
    }

    private function sanitize(string $filename): string
    {
        return preg_replace('/[^A-Za-z0-9._-]/', '_', $filename);
    }
}
