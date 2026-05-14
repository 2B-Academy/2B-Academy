<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\InstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Models\Instructor;
use App\Services\InstructorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InstructorController extends ApiController
{
    public function __construct(private readonly InstructorService $instructorService) {}

    public function index(Request $request): JsonResponse
    {
        $instructors = $this->instructorService->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
        );

        return $this->paginated(__('messages.retrieved'), InstructorResource::collection($instructors));
    }

    public function all(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            InstructorResource::collection($this->instructorService->all()),
        );
    }

    public function show(Instructor $instructor): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new InstructorResource($instructor->loadCount('courses')),
        );
    }

    public function store(InstructorRequest $request): JsonResponse
    {
        $instructor = $this->instructorService->create(
            $request->validated(),
            $request->file('image'),
        );

        return $this->created(
            __('messages.created'),
            new InstructorResource($instructor),
        );
    }

    public function update(InstructorRequest $request, Instructor $instructor): JsonResponse
    {
        $instructor = $this->instructorService->update(
            $instructor,
            $request->validated(),
            $request->file('image'),
        );

        return $this->success(
            __('messages.updated'),
            new InstructorResource($instructor),
        );
    }

    public function destroy(Instructor $instructor): JsonResponse
    {
        $this->instructorService->delete($instructor);
        return $this->deleted();
    }
}
