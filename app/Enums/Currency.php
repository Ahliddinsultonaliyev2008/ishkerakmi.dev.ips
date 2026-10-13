<?php

namespace App\Enums;

enum Currency: string
{
    use HasOptions;

    case UZS = 'UZS';
    case USD = 'USD';

    public function label(): string
    {
        return match ($this) {
            self::UZS => "so'm",
            self::USD => '$',
        };
    }
}
