<?php

namespace App\Http\Controllers\Concerns;

use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Support\Facades\Gate;

trait ResolvesEngageable
{
    protected function resolveEngageable(string $type, int $id, ?User $user): Vacancy|Resume
    {
        if ($type === 'vacancies') {
            return Vacancy::findOrFail($id);
        }

        $resume = Resume::findOrFail($id);
        abort_unless($resume->is_public || ($user && Gate::forUser($user)->allows('view', $resume)), 404);

        return $resume;
    }
}
