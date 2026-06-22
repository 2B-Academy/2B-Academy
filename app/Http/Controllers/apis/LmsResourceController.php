<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\LmsResourceRequest;
use App\Http\Resources\LmsResourceResource;
use App\Models\LmsResource;
use App\Services\LmsResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LmsResourceController extends ApiController
{
    public function __construct(private readonly LmsResourceService $service) {}

    /**
     * Paginated list of LMS resources (any authenticated user).
     */
    public function index(Request $request): JsonResponse
    {
        $resources = $this->service->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
            type:    $request->get('type'),
        );

        return $this->paginated(
            __('messages.retrieved'),
            LmsResourceResource::collection($resources),
        );
    }

    /**
     * Show a single LMS resource (any authenticated user).
     */
    public function show(LmsResource $lms_resource): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new LmsResourceResource($this->service->show($lms_resource)),
        );
    }

    /**
     * Create a new LMS resource (admin only).
     */
    public function store(LmsResourceRequest $request): JsonResponse
    {
        $resource = $this->service->create(
            $request->validated(),
            $request->user()->id,
            $request->file('file'),
        );

        return $this->created(
            __('messages.created'),
            new LmsResourceResource($resource->load('qualificationSkill:id,name')),
        );
    }

    /**
     * Update an LMS resource (admin only).
     */
    public function update(LmsResourceRequest $request, LmsResource $lms_resource): JsonResponse
    {
        $resource = $this->service->update(
            $lms_resource,
            $request->validated(),
            $request->file('file'),
        );

        return $this->success(
            __('messages.updated'),
            new LmsResourceResource($resource),
        );
    }

    /**
     * Delete an LMS resource (admin only).
     */
    public function destroy(LmsResource $lms_resource): JsonResponse
    {
        $this->service->delete($lms_resource);

        return $this->deleted();
    }
}
