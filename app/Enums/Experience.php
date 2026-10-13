<?php

namespace App\Enums;

enum Experience: string
{
    use HasOptions;

    case None = 'none';
    case OneToThree = '1_3';
    case ThreeToSix = '3_6';
    case SixPlus = '6_plus';

    public function label(): string
    {
        return match ($this) {
            self::None => 'Tajribasiz',
            self::OneToThree => '1–3 yil',
            self::ThreeToSix => '3–6 yil',
            self::SixPlus => '6 yildan ortiq',
        };
    }
}
