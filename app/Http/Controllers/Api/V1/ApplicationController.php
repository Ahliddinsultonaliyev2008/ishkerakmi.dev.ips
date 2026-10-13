<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\ApplicationStatus;
use App\Enums\VacancyStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Http\Requests\ApplicationStatusRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\Vacancy;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;

class ApplicationController extends Controller
{
    public function store(ApplicationRequest $request, Vacancy $vacancy): ApplicationResource
    {
        if ($vacancy->status !== VacancyStatus::Active) {
            throw ValidationException::withMessages(['vacancy' => 'Bu vakansiya yopilgan.']);
        }

        if ($vacancy->applications()->where('user_id', $request->user()->id)->exists()) {
            throw ValidationException::withMessages(['vacancy' => 'Siz bu vakansiyaga allaqachon ariza topshirgansiz.']);
        }

        $application = $vacancy->applications()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => ApplicationStatus::Pending,
        ]);

        return ApplicationResource::make($application->load(['vacancy.company', 'resume']));
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        $applications = $request->user()->applications()
            ->with(['vacancy.company', 'resume'])
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($this->perPage($request));

        return ApplicationResource::collection($applications);
    }

    public function incoming(Request $request): AnonymousResourceCollection
    {
        $applications = Application::query()
            ->whereHas('vacancy', fn ($q) => $q->where('company_id', $request->user()->company?->id))
            ->with(['vacancy', 'user', 'resume'])
            ->when($request->string('status')->toString(), fn ($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate($this->perPage($request));

        return ApplicationResource::collection($applications);
    }

    public function forVacancy(Request $request, Vacancy $vacancy): AnonymousResourceCollection
    {
        $this->authorize('viewApplications', $vacancy);

        return ApplicationResource::collection(
            $vacancy->applications()->with(['user', 'resume'])->latest()->paginate($this->perPage($request))
        );
    }

    public function updateStatus(ApplicationStatusRequest $request, Application $application): ApplicationResource
    {
        $this->authorize('update', $application);

        $application->update($request->validated());

        return ApplicationResource::make($application->load(['vacancy', 'user', 'resume']));
    }

    public function destroy(Application $application): Response
    {
        $this->authorize('delete', $application);

        $application->delete();

        return response()->noContent();
    }
}
