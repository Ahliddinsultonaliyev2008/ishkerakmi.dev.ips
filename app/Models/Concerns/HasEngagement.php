<?php

namespace App\Models\Concerns;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\MorphMany;

/**
 * Comments and likes for vacancies and resumes.
 */
trait HasEngagement
{
    public static function bootHasEngagement(): void
    {
        static::deleting(function ($model) {
            $model->comments()->delete();
            $model->likes()->delete();
        });
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')->latest();
    }

    public function likes(): MorphMany
    {
        return $this->morphMany(Like::class, 'likeable');
    }

    public function scopeWithEngagement(Builder $query, ?User $user): Builder
    {
        $query->withCount(['likes', 'comments']);

        if ($user) {
            $query->withExists(['likes as liked' => fn (Builder $q) => $q->where('user_id', $user->id)]);
        }

        return $query;
    }
}
