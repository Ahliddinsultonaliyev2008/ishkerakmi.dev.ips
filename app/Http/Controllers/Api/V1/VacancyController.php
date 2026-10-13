<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\VacancyRequest;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class VacancyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $vacancies = Vacancy::query()
            ->active()
            ->with(['company', 'category'])
            ->withEngagement($request->user('sanctum'))
            ->filter($request->only(['q', 'city', 'category', 'employment_type', 'experience', 'company', 'salary_min', 'sort']))
            ->paginate($this->perPage($request))
            ->withQueryString();

        return VacancyResource::collection($vacancies);
    }

    public function show(Request $request, Vacancy $vacancy): VacancyResource
    {
        $vacancy->increment('views');
        $user = $request->user('sanctum');

        $vacancy = Vacancy::whereKey($vacancy->id)
            ->with(['company' => fn ($q) => $q->withCount('vacancies'), 'category'])
            ->withEngagement($user)
            ->when($user, fn ($q) => $q->withExists(['applications as has_applied' => fn ($q) => $q->where('user_id', $user->id)]))
            ->firstOrFail();

        return VacancyResource::make($vacancy);
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $vacancies = Vacancy::query()
            ->where('company_id', $request->user()->company?->id)
            ->with('category')
            ->withCount(['applications', 'likes', 'comments'])
            ->latest()
            ->paginate($this->perPage($request));

        return VacancyResource::collection($vacancies);
    }

    public function store(VacancyRequest $request): VacancyResource
    {
        $this->authorize('create', Vacancy::class);

        $vacancy = $request->user()->company->vacancies()->create($request->validated());

        return VacancyResource::make($vacancy->load(['company', 'category']));
    }

    public function update(VacancyRequest $request, Vacancy $vacancy): VacancyResource
    {
        $this->authorize('update', $vacancy);

        $vacancy->update($request->validated());

        return VacancyResource::make($vacancy->load(['company', 'category']));
    }

    public function destroy(Vacancy $vacancy): Response
    {
        $this->authorize('delete', $vacancy);

        $vacancy->delete();

        return response()->noContent();
    }
}
