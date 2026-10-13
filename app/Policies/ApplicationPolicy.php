<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

class ApplicationPolicy
{
    public function update(User $user, Application $application): bool
    {
        return $user->company?->id === $application->vacancy->company_id;
    }

    public function delete(User $user, Application $application): bool
    {
        return $user->id === $application->user_id;
    }
}
