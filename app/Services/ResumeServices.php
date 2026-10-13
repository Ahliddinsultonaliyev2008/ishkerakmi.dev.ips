<?php

namespace App\Services;

use App\Models\Resume;
use Illuminate\Http\JsonResponse;

class ResumeServices
{
    function show($id): JsonResponse
    {
        $resume = Resume::where('id', $id)->with('comments')->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);

        return response()->json([
            'success' => true,
            'resume' => $resume,
        ]);
    }

    function all(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'resumes' => Resume::all(),
        ]);
    }

    function destroy($id): JsonResponse
    {
        $resume = Resume::where('id', $id)->where('user_id', auth('sanctum')->id())->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);

        $resume->delete();

        return response()->json([
            'success' => true,
            'massage' => "Resume delete success"
        ]);
    }

    function store(array $data): JsonResponse
    {
        $resume = Resume::create([
            ...$data,
            'user_id' => auth('sanctum')->id(),
        ]);

        if (!$resume->exists)
            return response()->json([
                'success' => false,
                'massage' => "Create resume error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Resume creation success",
            'id' => $resume->id,
        ], 201);
    }

    function update(array $data, $id): JsonResponse
    {
        $resume = Resume::where('id', $id)->where('user_id', auth('sanctum')->id())->first();

        if (is_null($resume))
            return response()->json([
                'success' => false,
                'massage' => "Resume not found"
            ], 404);

        if (!$resume->update($data))
            return response()->json([
                'success' => false,
                'massage' => "Update resume error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Update resume success",
            'id' => $resume->id,
        ]);
    }
}
