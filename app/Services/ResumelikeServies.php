<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Like;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class resumelikeServies
{
    function store($post): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $like = Like::create([
            'user_id' => $user->id,
            'resume_id' => $post['resume_id'],
            'likes' => $post['likes'],
            ]);


        if (!$like->exists())
            return response()->json([
                'success' => false,
                'massage' => "Create resume like error"
            ], 500);


        return response()->json([
            'success' => true,
            'massage' => "Resume like creation success",
            'id' => $like->id,
        ]);
    }
    function destroy($comment_id): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $like = Like::where('id', $comment_id)-> where('user_id', $user_id)-> first();
        if (is_null($like))
            return response()->json([
                'success' => false,
                'massage' => "Like not fount"
            ], 404);

        if (!$like->delete())
            return response()->json([
                'success' => false,
                'massage' => "Delete resume like error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Resume like delete success",
            'id' => $like->id,
        ]);
    }
}
