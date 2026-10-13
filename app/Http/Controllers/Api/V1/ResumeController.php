<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\ResumeRequest;
use App\Http\Resources\ResumeResource;
use App\Models\Resume;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;

class ResumeController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $resumes = Resume::query()
            ->where('is_public', true)
            ->with('category')
            ->withEngagement($request->user())
            ->filter($request->only(['q', 'city', 'category', 'employment_type', 'experience']))
            ->paginate($this->perPage($request))
            ->withQueryString();

        return ResumeResource::collection($resumes);
    }

    public function show(Request $request, Resume $resume): ResumeResource
    {
        $this->authorize('view', $resume);

        $resume = Resume::whereKey($resume->id)->with('category')->withEngagement($request->user())->firstOrFail();

        return ResumeResource::make($resume);
    }

    public function mine(Request $request): AnonymousResourceCollection
    {
        return ResumeResource::collection(
            $request->user()->resumes()->with('category')->withCount(['likes', 'comments'])->latest()->get()
        );
    }

    public function store(ResumeRequest $request): ResumeResource
    {
        $resume = $request->user()->resumes()->create($request->validated());

        return ResumeResource::make($resume->load('category'));
    }

    public function update(ResumeRequest $request, Resume $resume): ResumeResource
    {
        $this->authorize('update', $resume);

        $resume->update($request->validated());

        return ResumeResource::make($resume->load('category'));
    }

    public function destroy(Resume $resume): Response
    {
        $this->authorize('delete', $resume);

        $resume->delete();

        return response()->noContent();
    }
}
