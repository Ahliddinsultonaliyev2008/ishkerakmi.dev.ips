<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ResolvesEngageable;
use App\Http\Controllers\Controller;
use App\Http\Resources\VacancyResource;
use App\Models\Vacancy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class LikeController extends Controller
{
    use ResolvesEngageable;

    public function toggle(Request $request, string $type, int $id): JsonResponse
    {
        $model = $this->resolveEngageable($type, $id, $request->user());
        $like = $model->likes()->where('user_id', $request->user()->id)->first();

        $like ? $like->delete() : $model->likes()->create(['user_id' => $request->user()->id]);

        return response()->json([
            'liked' => ! $like,
            'likes_count' => $model->likes()->count(),
        ]);
    }

    public function saved(Request $request): AnonymousResourceCollection
    {
        $vacancies = Vacancy::query()
            ->whereHas('likes', fn ($q) => $q->where('user_id', $request->user()->id))
            ->with(['company', 'category'])
            ->withEngagement($request->user())
            ->latest()
            ->paginate($this->perPage($request));

        return VacancyResource::collection($vacancies);
    }
}
