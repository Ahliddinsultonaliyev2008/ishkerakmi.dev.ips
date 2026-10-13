<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyRequest;
use App\Http\Resources\CompanyResource;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CompanyController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $companies = Company::query()
            ->withCount(['vacancies' => fn ($q) => $q->active()])
            ->when($request->string('q')->toString(), fn ($q, $term) => $q->where('name', 'like', "%{$term}%"))
            ->when($request->string('city')->toString(), fn ($q, $city) => $q->where('city', $city))
            ->orderByDesc('vacancies_count')
            ->paginate($this->perPage($request))
            ->withQueryString();

        return CompanyResource::collection($companies);
    }

    public function show(Company $company): CompanyResource
    {
        return CompanyResource::make($company->loadCount(['vacancies' => fn ($q) => $q->active()]));
    }

    public function mine(Request $request): CompanyResource
    {
        return CompanyResource::make($request->user()->company()->firstOrFail()->loadCount('vacancies'));
    }

    public function update(CompanyRequest $request): CompanyResource
    {
        $company = $request->user()->company()->updateOrCreate([], $request->validated());

        return CompanyResource::make($company);
    }
}
