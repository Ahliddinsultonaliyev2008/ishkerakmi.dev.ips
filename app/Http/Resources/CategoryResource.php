<?php

namespace App\Http\Resources;

use App\Models\Category;
use Illuminate\Http\Request;

/** @mixin Category */
class CategoryResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'vacancies_count' => $this->whenCounted('vacancies'),
        ];
    }
}
