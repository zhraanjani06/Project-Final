<?php

namespace App\Helpers;

class FormatHelper
{
    /**
     * Format a number to a max of 2 decimal places.
     */
    public static function decimal($number, $decimals = 2)
    {
        if (!is_numeric($number)) return $number;
        return number_format((float)$number, $decimals, '.', ',');
    }

    /**
     * Format a number as a percentage (e.g. 4.20%).
     */
    public static function percentage($number, $decimals = 2)
    {
        if (!is_numeric($number)) return $number . '%';
        return number_format((float)$number, $decimals, '.', ',') . '%';
    }

    /**
     * Format a large number to US$ Trillion/Billion/Million format.
     */
    public static function gdp($number)
    {
        if (!is_numeric($number)) return $number;
        $num = (float)$number;

        if ($num >= 1e12) {
            return 'US$' . number_format($num / 1e12, 2, '.', ',') . ' Trillion';
        } elseif ($num >= 1e9) {
            return 'US$' . number_format($num / 1e9, 2, '.', ',') . ' Billion';
        } elseif ($num >= 1e6) {
            return 'US$' . number_format($num / 1e6, 2, '.', ',') . ' Million';
        }

        return 'US$' . number_format($num, 2, '.', ',');
    }

    /**
     * Format currency exchange rate (1 USD = X Currency).
     */
    public static function currency($rate, $code)
    {
        if (!is_numeric($rate)) return $rate;
        return '1 USD = ' . number_format((float)$rate, 2, '.', ',') . ' ' . $code;
    }
}
