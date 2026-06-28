<?php

if (!function_exists('currency_sym')) {
    function currency_sym(string $code): string
    {
        return match(strtoupper($code)) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'SAR' => 'SR',
            'AED' => 'AED',
            'KWD' => 'KWD',
            'QAR' => 'QAR',
            'OMR' => 'OMR',
            'BHD' => 'BHD',
            'EGP' => 'EGP',
            default => $code,
        };
    }
}

if (!function_exists('fmt_money')) {
    function fmt_money(float $amount, string $currency = 'SAR'): string
    {
        return currency_sym($currency) . ' ' . number_format($amount, 2);
    }
}
