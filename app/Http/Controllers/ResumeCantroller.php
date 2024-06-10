<?php

namespace App\Http\Controllers;

use App\Services\ResumeServices;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResumeCantroller extends Controller
{
    public function __construct(protected ResumeServices $resumeServices)
    {
    }

    function show($id): JsonResponse
    {
        return $this ->resumeServices ->show($id);
    }

    function all(): JsonResponse
    {
        return $this ->resumeServices ->all();
    }

    function store(Request $request): JsonResponse
    {
        return $this->resumeServices->store($request ->post());
    }

    function update (Request $request, $id): JsonResponse
    {
        return $this->resumeServices->update($request ->post(), $id);
    }
    function destroy($id): JsonResponse
    {
        return $this ->resumeServices ->destroy($id);
    }
}
