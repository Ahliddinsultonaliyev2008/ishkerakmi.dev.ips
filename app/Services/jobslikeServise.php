<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class jobslikeServise
{
    public function store($post): JsonResponse
    {
        $company_id = session('company_id');
        if (!isset($post['likes'])) {
            return response()->json([
                'success' => false,
                'message' => "like not fount"
            ], 400);
        }

        $like = Like::create([
            'company_id' => $company_id,
            'job_id' => $post['job_id'],
            'likes' => $post['likes'],
        ]);

        if (!$like) {
            return response()->json([
                'success' => false,
                'message' => "Create jobs like error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Jobs like creation success",
            'id' => $like->id,
        ]);
    }
    function destroy($comment_id): JsonResponse
    {
        $company_id = session('company_id');

        if (is_null($company_id))
            return response()->json([
                'success' => false,
                'massage' => "Company not found"
            ], 404);

        $like = Like::where('id', $comment_id)->first();
        if (is_null($like))
            return response()->json([
                'success' => false,
                'massage' => "like not fount"
            ], 404);

        if (!$like->delete())
            return response()->json([
                'success' => false,
                'massage' => "Delete job like error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Job like delete success",
            'id' => $like->id,
        ]);
    }
}
