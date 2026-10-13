<?php

namespace App\Enums;

enum VacancyStatus: string
{
    use HasOptions;

    case Active = 'active';
    case Closed = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::Active => 'Faol',
            self::Closed => 'Yopilgan',
        };
    }
}
