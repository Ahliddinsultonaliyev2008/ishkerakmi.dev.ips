<?php

namespace App\Http\Resources;

use App\Models\Comment;
use Illuminate\Http\Request;

/** @mixin Comment */
class CommentResource extends Resource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'body' => $this->body,
            'author' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'role' => $this->enum($this->user->role),
            ],
            'can_edit' => $request->user('sanctum')?->id === $this->user_id,
            'created_at' => $this->created_at,
        ];
    }
}
