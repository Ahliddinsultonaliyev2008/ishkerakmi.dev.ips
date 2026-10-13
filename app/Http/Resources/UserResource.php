<?php

namespace App\Http\Resources;

use App\Models\User;
use Illuminate\Http\Request;

/** @mixin User */
class UserResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'phone' => $this->phone,
            'email' => $this->email,
            'role' => $this->enum($this->role),
            'city' => $this->city,
            'avatar' => $this->avatar,
            'company' => CompanyResource::make($this->whenLoaded('company')),
            'created_at' => $this->created_at,
        ];
    }
}
