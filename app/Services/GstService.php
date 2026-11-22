<?php

namespace App\Services;

class GstService
{
    /**
     * Calculate GST breakdown based on seller state code and buyer/branch state code.
     */
    public static function calculate(
        float $taxableAmount,
        float $gstRate,
        string $sellerStateCode = '27', // Default Maharashtra
        ?string $buyerStateCode = '27'
    ): array {
        $taxable = round($taxableAmount, 2);
        $rate = round($gstRate, 2);
        $totalTax = round(($taxable * $rate) / 100, 2);

        $isInterState = !empty($buyerStateCode) && ($sellerStateCode !== $buyerStateCode);

        if ($isInterState) {
            return [
                'taxable' => $taxable,
                'rate' => $rate,
                'cgst_rate' => 0.0,
                'cgst' => 0.0,
                'sgst_rate' => 0.0,
                'sgst' => 0.0,
                'igst_rate' => $rate,
                'igst' => $totalTax,
                'total_tax' => $totalTax,
                'is_inter_state' => true,
            ];
        }

        $halfRate = round($rate / 2, 2);
        $cgst = round($totalTax / 2, 2);
        $sgst = round($totalTax - $cgst, 2);

        return [
            'taxable' => $taxable,
            'rate' => $rate,
            'cgst_rate' => $halfRate,
            'cgst' => $cgst,
            'sgst_rate' => $halfRate,
            'sgst' => $sgst,
            'igst_rate' => 0.0,
            'igst' => 0.0,
            'total_tax' => $totalTax,
            'is_inter_state' => false,
        ];
    }
}
