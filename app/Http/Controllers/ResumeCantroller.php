<?php

namespace App\Http\Controllers;

use App\Http\Requests\ResumeRequest;
use App\Services\ResumeServices;
use Illuminate\Http\JsonResponse;

class ResumeCantroller extends Controller
{
    public function __construct(protected ResumeServices $resumeServices)
    {
    }

    function show($id): JsonResponse
    {
        return $this->resumeServices->show($id);
    }

    function all(): JsonResponse
    {
        return $this->resumeServices->all();
    }

    function store(ResumeRequest $request): JsonResponse
    {
        return $this->resumeServices->store($request->validated());
    }

    function update(ResumeRequest $request, $id): JsonResponse
    {
        return $this->resumeServices->update($request->validated(), $id);
    }

    function destroy($id): JsonResponse
    {
        return $this->resumeServices->destroy($id);
    }
}
