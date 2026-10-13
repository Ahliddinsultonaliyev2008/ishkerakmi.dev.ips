<?php

namespace App\Http\Resources;

use App\Models\Application;
use Illuminate\Http\Request;

/** @mixin Application */
class ApplicationResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->enum($this->status),
            'cover_letter' => $this->cover_letter,
            'vacancy' => VacancyResource::make($this->whenLoaded('vacancy')),
            'applicant' => UserResource::make($this->whenLoaded('user')),
            'resume' => ResumeResource::make($this->whenLoaded('resume')),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
