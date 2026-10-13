<?php

namespace App\Http\Requests\Concerns;

trait NormalizesPhone
{
    protected function normalizePhone(?string $phone): ?string
    {
        if ($phone === null || $phone === '') {
            return $phone;
        }

        $digits = preg_replace('/\D/', '', $phone);

        return '+'.(strlen($digits) === 9 ? '998'.$digits : $digits);
    }
}
