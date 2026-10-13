<?php

namespace App\Enums;

enum EmploymentType: string
{
    use HasOptions;

    case FullTime = 'full_time';
    case PartTime = 'part_time';
    case Remote = 'remote';
    case Internship = 'internship';
    case Contract = 'contract';

    public function label(): string
    {
        return match ($this) {
            self::FullTime => "To'liq stavka",
            self::PartTime => 'Yarim stavka',
            self::Remote => 'Masofaviy',
            self::Internship => 'Amaliyot',
            self::Contract => 'Shartnoma asosida',
        };
    }
}
