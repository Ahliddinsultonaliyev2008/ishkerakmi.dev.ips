<?php

namespace App\Http\Resources;

use App\Models\Vacancy;
use Illuminate\Http\Request;

/** @mixin Vacancy */
class VacancyResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'benefits' => $this->benefits,
            'city' => $this->city,
            'address' => $this->address,
            'employment_type' => $this->enum($this->employment_type),
            'experience' => $this->enum($this->experience),
            'salary_from' => $this->salary_from,
            'salary_to' => $this->salary_to,
            'currency' => $this->enum($this->currency),
            'skills' => $this->skills ?? [],
            'status' => $this->enum($this->status),
            'views' => $this->views,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'likes_count' => $this->whenCounted('likes'),
            'comments_count' => $this->whenCounted('comments'),
            'applications_count' => $this->whenCounted('applications'),
            'liked' => (bool) ($this->liked ?? false),
            'has_applied' => $this->whenHas('has_applied', fn () => (bool) $this->has_applied),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
