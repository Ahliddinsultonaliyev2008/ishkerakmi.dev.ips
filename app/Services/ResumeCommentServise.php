<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class ResumeCommentServise
{
    function index($post): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $comments = Comment::where('resume_id', $post['resume_id'])->get() ->all();

        if (empty($comments))
            return response()->json([
                'success' => false,
                'massage' => "Comments not found"
            ], 404);


        return response()->json([
            'success' => true,
            'comments' => $comments,
        ]);

    }
    function store($post): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $comment = Comment::create([
            'user_id' => $user->id,
            'resume_id' => $post['resume_id'],
            'content' => $post['content'],
        ]);


        if (!$comment->exists())
            return response()->json([
                'success' => false,
                'massage' => "Create resume comment error"
            ], 500);


        return response()->json([
            'success' => true,
            'massage' => "Resume comment creation success",
            'id' => $comment->id,
        ]);
    }
    function update($post, $comment_id): JsonResponse
    {
        $user_id = auth('sanctum')->user()->id;
        $user = User::where('id', $user_id)->first();

        if (is_null($user))
            return response()->json([
                'success' => false,
                'massage' => "User not found"
            ], 404);

        $comment = Comment::where('id', $comment_id)-> first();


        if (is_null($comment))
            return response()->json([
                'success' => false,
                'massage' => "Comment not fount"
            ], 404);

        $comment ->content = $post['content'];

        if (!$comment->save())
            return response()->json([
                'success' => false,
                'massage' => "Update resume comment error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Resume comment update  success",
            'id' => $comment->id,
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

        $comment = Comment::where('id', $comment_id)-> where('user_id', $user_id)-> first();
        if (is_null($comment))
            return response()->json([
                'success' => false,
                'massage' => "Comment not fount"
            ], 404);

        if (!$comment->delete())
            return response()->json([
                'success' => false,
                'massage' => "Delete resume comment error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Resume comment delete success",
            'id' => $comment->id,
        ]);
    }
}
