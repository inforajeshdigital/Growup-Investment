<?php

namespace App\Support;

/** Converts an amount to Indian-style words, e.g. 150000 => "Rupees One Lakh Fifty Thousand Only". */
class AmountInWords
{
    private const ONES = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
        'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];

    private const TENS = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

    public static function convert(float|string $amount): string
    {
        $amount = round((float) $amount, 2);
        $rupees = (int) floor($amount);
        $paise = (int) round(($amount - $rupees) * 100);

        $words = 'Rupees '.($rupees === 0 ? 'Zero' : self::number($rupees));
        if ($paise > 0) {
            $words .= ' and '.self::number($paise).' Paise';
        }

        return $words.' Only';
    }

    private static function number(int $n): string
    {
        $parts = [];
        foreach ([[10000000, 'Crore'], [100000, 'Lakh'], [1000, 'Thousand'], [100, 'Hundred']] as [$value, $label]) {
            if ($n >= $value) {
                $count = intdiv($n, $value);
                // The crore count can be larger than 99, so convert it recursively
                $parts[] = ($count > 99 ? self::number($count) : self::twoDigits($count)).' '.$label;
                $n %= $value;
            }
        }
        if ($n > 0) {
            $parts[] = self::twoDigits($n);
        }

        return implode(' ', $parts);
    }

    private static function twoDigits(int $n): string
    {
        if ($n < 20) {
            return self::ONES[$n];
        }

        return trim(self::TENS[intdiv($n, 10)].' '.self::ONES[$n % 10]);
    }
}
