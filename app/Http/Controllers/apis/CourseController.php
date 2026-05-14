<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseRequest;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseController extends ApiController
{
    public function __construct(private readonly CourseService $courseService) {}

    public function index(Request $request): JsonResponse
    {
        $courses = $this->courseService->list(
            perPage:    (int) $request->get('per_page', 15),
            search:     $request->get('search'),
            categoryId: $request->integer('category_id') ?: null,
            active:     $request->has('active') ? filter_var($request->active, FILTER_VALIDATE_BOOLEAN) : null,
            courseType: $request->get('course_type'),
        );

        return $this->paginated(__('messages.retrieved'), CourseResource::collection($courses));
    }

    public function show(Course $course): JsonResponse
    {
        $course = $this->courseService->findOrFail($course->id);

        return $this->success(
            __('messages.retrieved'),
            new CourseDetailResource($course),
        );
    }

    public function store(CourseRequest $request): JsonResponse
    {
        $course = $this->courseService->create(
            $request->validated(),
            $request->file('image'),
        );

        return $this->created(
            __('messages.created'),
            new CourseResource($course),
        );
    }

    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        $course = $this->courseService->update(
            $course,
            $request->validated(),
            $request->file('image'),
        );

        return $this->success(
            __('messages.updated'),
            new CourseResource($course),
        );
    }

    public function destroy(Course $course): JsonResponse
    {
        $this->courseService->delete($course);
        return $this->deleted();
    }
}
