<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vacancy;

class VacancyPolicy
{
    public function create(User $user): bool
    {
        return $user->isEmployer() && $user->company()->exists();
    }

    public function update(User $user, Vacancy $vacancy): bool
    {
        return $user->isEmployer() && $user->company?->id === $vacancy->company_id;
    }

    public function delete(User $user, Vacancy $vacancy): bool
    {
        return $this->update($user, $vacancy);
    }

    public function viewApplications(User $user, Vacancy $vacancy): bool
    {
        return $this->update($user, $vacancy);
    }
}
