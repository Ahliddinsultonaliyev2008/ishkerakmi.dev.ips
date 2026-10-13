<?php

namespace App\Enums;

enum UserRole: string
{
    use HasOptions;

    case Seeker = 'seeker';
    case Employer = 'employer';

    public function label(): string
    {
        return match ($this) {
            self::Seeker => 'Ish izlovchi',
            self::Employer => 'Ish beruvchi',
        };
    }
}
