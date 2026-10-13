<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;

/** @mixin Company */
class CompanyResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'logo' => $this->logo,
            'industry' => $this->industry,
            'city' => $this->city,
            'website' => $this->website,
            'email' => $this->email,
            'phone' => $this->phone,
            'employees_count' => $this->employees_count,
            'description' => $this->description,
            'is_verified' => $this->is_verified,
            'vacancies_count' => $this->whenCounted('vacancies'),
            'created_at' => $this->created_at,
        ];
    }
}
