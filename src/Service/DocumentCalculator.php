<?php

namespace App\Service;

/**
 * Computes the per-VAT-rate breakdown (Taux / Base / TVA) shown on printed
 * documents. This used to be done in client-side JS on the print page; doing it
 * in PHP lets the document render server-side as a PDF.
 */
class DocumentCalculator
{
    /**
     * @param iterable $lignes line items exposing getPrixUnitaire(), getQte(),
     *                         getRemise() (percent) and getTva() (percent)
     *
     * @return array<int, array{taux: float, base: float, tva: float}>
     *         one entry per distinct VAT rate, base = HT after line discount
     */
    public function tvaBreakdown(iterable $lignes): array
    {
        $rows = [];
        foreach ($lignes as $ligne) {
            $pu = (float) $ligne->getPrixUnitaire();
            $qte = (float) $ligne->getQte();
            $remise = (float) $ligne->getRemise();
            // getTva() may return a Tva entity (its __toString is the rate) or a scalar.
            $taux = (float) (string) $ligne->getTva();

            $htBeforeDiscount = $pu * $qte;
            $base = $htBeforeDiscount - ($htBeforeDiscount / 100 * $remise);
            $tva = $base / 100 * $taux;

            $key = (string) $taux;
            if (!isset($rows[$key])) {
                $rows[$key] = ['taux' => $taux, 'base' => 0.0, 'tva' => 0.0];
            }
            $rows[$key]['base'] += $base;
            $rows[$key]['tva'] += $tva;
        }

        return array_values($rows);
    }
}
