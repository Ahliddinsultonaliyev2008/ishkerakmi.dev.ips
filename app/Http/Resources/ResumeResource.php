<?php

namespace App\Http\Resources;

use App\Models\Resume;
use Illuminate\Http\Request;

/** @mixin Resume */
class ResumeResource extends Resource
{
    public function toArray(Request $request): array
    {
        $viewer = $request->user();
        $showContacts = $viewer && ($viewer->id === $this->user_id || $viewer->isEmployer());

        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'title' => $this->title,
            'full_name' => $this->full_name,
            'phone' => $this->when($showContacts, $this->phone),
            'email' => $this->when($showContacts, $this->email),
            'city' => $this->city,
            'birth_date' => $this->birth_date?->toDateString(),
            'age' => $this->birth_date?->age,
            'gender' => $this->gender,
            'employment_type' => $this->enum($this->employment_type),
            'experience' => $this->enum($this->experience),
            'salary_expected' => $this->salary_expected,
            'currency' => $this->enum($this->currency),
            'skills' => $this->skills ?? [],
            'about' => $this->about,
            'is_public' => $this->is_public,
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'likes_count' => $this->whenCounted('likes'),
            'comments_count' => $this->whenCounted('comments'),
            'liked' => (bool) ($this->liked ?? false),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
