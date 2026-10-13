<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ResolvesEngageable;
use App\Http\Controllers\Controller;
use App\Http\Requests\CommentRequest;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class CommentController extends Controller
{
    use ResolvesEngageable;

    public function index(Request $request, string $type, int $id): AnonymousResourceCollection
    {
        $model = $this->resolveEngageable($type, $id, $request->user('sanctum'));

        return CommentResource::collection($model->comments()->with('user')->paginate($this->perPage($request)));
    }

    public function store(CommentRequest $request, string $type, int $id): CommentResource
    {
        $model = $this->resolveEngageable($type, $id, $request->user());

        $comment = $model->comments()->create([...$request->validated(), 'user_id' => $request->user()->id]);

        return CommentResource::make($comment->load('user'));
    }

    public function update(CommentRequest $request, Comment $comment): CommentResource
    {
        $this->authorize('update', $comment);

        $comment->update($request->validated());

        return CommentResource::make($comment->load('user'));
    }

    public function destroy(Comment $comment): Response
    {
        $this->authorize('delete', $comment);

        $comment->delete();

        return response()->noContent();
    }
}
