<?php

namespace App\Http\Controllers;

use App\Services\jobsCompanyServices;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class jobsCompanyCantroller extends Controller
{
    public function __construct(protected jobsCompanyServices $jobsCompanyServices)
    {
    }

    function show($id): JsonResponse
    {
        return $this ->jobsCompanyServices ->show($id);
    }

    function all(): JsonResponse
    {
        return $this ->jobsCompanyServices ->all();
    }

    function store(Request $request): JsonResponse
    {
        return $this->jobsCompanyServices->store($request ->post());
    }

    function update (Request $request, $id): JsonResponse
    {
        return $this->jobsCompanyServices->update($request ->post(), $id);
    }
    function destroy($id): JsonResponse
    {
        return $this ->jobsCompanyServices ->destroy($id);
    }
}
