<?php

namespace App\Policies;

use App\Models\Resume;
use App\Models\User;

class ResumePolicy
{
    public function view(User $user, Resume $resume): bool
    {
        return $resume->is_public
            || $user->id === $resume->user_id
            || $resume->user->applications()->whereHas('vacancy.company', fn ($q) => $q->where('user_id', $user->id))->exists();
    }

    public function update(User $user, Resume $resume): bool
    {
        return $user->id === $resume->user_id;
    }

    public function delete(User $user, Resume $resume): bool
    {
        return $this->update($user, $resume);
    }
}
