<?php

namespace App\Services;

use App\Models\Jobs;
use Illuminate\Http\JsonResponse;

class jobsCompanyServices
{
    public function show($id): JsonResponse
    {
        $job = Jobs::where('id', $id)->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "Job not found"
            ], 404);
        }

        return response()->json([
            'success' => true,
            'job' => $job,
        ]);
    }

    public function all(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'jobs' => Jobs::all(),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $job = Jobs::where('id', $id)->where('company_id', session('company_id'))->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "Job not found"
            ], 404);
        }

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => "Job delete success"
        ]);
    }

    public function store(array $data): JsonResponse
    {
        $job = Jobs::create([
            ...$data,
            'company_id' => session('company_id'),
        ]);

        if (!$job->exists) {
            return response()->json([
                'success' => false,
                'message' => "Create job error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Job creation success",
            'id' => $job->id,
        ], 201);
    }

    public function update(array $data, $id): JsonResponse
    {
        $job = Jobs::where('id', $id)->where('company_id', session('company_id'))->first();

        if (is_null($job)) {
            return response()->json([
                'success' => false,
                'message' => "Job not found"
            ], 404);
        }

        if (!$job->update($data)) {
            return response()->json([
                'success' => false,
                'message' => "Update job error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Update job success",
            'id' => $job->id,
        ]);
    }
}
