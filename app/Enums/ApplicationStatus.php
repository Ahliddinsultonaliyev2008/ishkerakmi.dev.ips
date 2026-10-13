<?php

namespace App\Enums;

enum ApplicationStatus: string
{
    use HasOptions;

    case Pending = 'pending';
    case Interview = 'interview';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    public function label(): string
    {
        return match ($this) {
            self::Pending => "Ko'rib chiqilmoqda",
            self::Interview => 'Suhbatga taklif',
            self::Accepted => 'Qabul qilindi',
            self::Rejected => 'Rad etildi',
        };
    }
}
