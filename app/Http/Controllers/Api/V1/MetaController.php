<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApplicationStatus;
use App\Enums\Currency;
use App\Enums\EmploymentType;
use App\Enums\Experience;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Models\Company;
use App\Models\Resume;
use App\Models\User;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;

class MetaController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'cities' => config('ishkerakmi.cities'),
            'categories' => CategoryResource::collection(
                Category::withCount(['vacancies' => fn ($q) => $q->active()])->orderBy('name')->get()
            ),
            'employment_types' => EmploymentType::options(),
            'experiences' => Experience::options(),
            'currencies' => Currency::options(),
            'application_statuses' => ApplicationStatus::options(),
            'roles' => UserRole::options(),
            'stats' => [
                'vacancies' => Vacancy::active()->count(),
                'companies' => Company::count(),
                'resumes' => Resume::where('is_public', true)->count(),
                'seekers' => User::where('role', UserRole::Seeker)->count(),
            ],
        ]);
    }
}
