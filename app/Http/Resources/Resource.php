<?php

namespace App\Http\Resources;

use BackedEnum;
use Illuminate\Http\Resources\Json\JsonResource;

abstract class Resource extends JsonResource
{
    /**
     * @return array{value: string, label: string}|null
     */
    protected function enum(?BackedEnum $case): ?array
    {
        return $case ? ['value' => $case->value, 'label' => $case->label()] : null;
    }
}
