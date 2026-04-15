<?php

namespace App\Enums;

enum NumEnum: int
{
    case PRECITION = 1;

    /**
     * Convierte un número a formato abreviado (1K, 1.5M, etc.)
     *
     * @param float|int $num
     * @param int $precision
     * @return string
     */
    static function letter_format($num, int $precision = self::PRECITION->value): string
    {
        if (!is_numeric($num)) {
            return '0';
        }

        if ($num >= 1000000000) {
            return round($num / 1000000000, $precision) . 'B';
        }
        if ($num >= 1000000) {
            return round($num / 1000000, $precision) . 'M';
        }
        if ($num >= 1000) {
            return round($num / 1000, $precision) . 'K';
        }

        return (string) $num;
    }
}