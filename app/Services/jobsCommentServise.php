<?php

namespace App\Services;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;

class jobsCommentServise
{
    function index($post): JsonResponse
    {
        $company_id = session('company_id');

        if (is_null($company_id))
            return response()->json([
                'success' => false,
                'massage' => "Company not found"
            ], 404);

        $comments = Comment::where('job_id', $post['job_id'])->get()->all();

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

        $company_id = session('company_id');

        $comment = Comment::create([
            'company_id' => $company_id,
            'job_id' => $post['job_id'],
            'content' => $post['content'],
        ]);

        if (!$comment->exists()) {
            return response()->json([
                'success' => false,
                'message' => "Create jobs comment error"
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Jobs comment creation success",
            'id' => $comment->id,
        ]);
    }

    function update($post, $comment_id): JsonResponse
    {
        $company_id = session('company_id');

        if (is_null($company_id))
            return response()->json([
                'success' => false,
                'massage' => "Company not found"
            ], 404);

        $comment = Comment::where('id', $comment_id)->first();


        if (is_null($comment))
            return response()->json([
                'success' => false,
                'massage' => "Comment not fount"
            ], 404);

        $comment->content = $post['content'];

        if (!$comment->save())
            return response()->json([
                'success' => false,
                'massage' => "Update jobs comment error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Jobs comment update success",
            'id' => $comment->id,
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

        $comment = Comment::where('id', $comment_id)->first();
        if (is_null($comment))
            return response()->json([
                'success' => false,
                'massage' => "Comment not fount"
            ], 404);

        if (!$comment->delete())
            return response()->json([
                'success' => false,
                'massage' => "Delete jobs comment error"
            ], 500);

        return response()->json([
            'success' => true,
            'massage' => "Jobs comment delete success",
            'id' => $comment->id,
        ]);
    }
}
