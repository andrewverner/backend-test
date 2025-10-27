<?php

declare(strict_types=1);

namespace App\Enum;

enum CountriesCodeEnum: string
{
    case DE = 'DE';
    case IT = 'IT';
    case FR = 'FR';
    case GR = 'GR';

    public function getTaxAmount(): float
    {
        return match ($this) {
            self::DE => 0.19,
            self::IT => 0.22,
            self::FR => 0.20,
            self::GR => 0.24,
        };
    }
}
