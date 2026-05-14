<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseLectureRequest;
use App\Http\Resources\CourseLectureResource;
use App\Http\Resources\CourseSectionResource;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Services\CourseLectureService;
use Illuminate\Http\JsonResponse;

class CourseLectureController extends ApiController
{
    public function __construct(private readonly CourseLectureService $service) {}

    // GET /courses/{course}/lectures  — grouped by section
    public function index(Course $course): JsonResponse
    {
        $sections = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseSectionResource::collection($sections));
    }

    // POST /courses/{course}/lectures
    public function store(Course $course, CourseLectureRequest $request): JsonResponse
    {
        $lecture = $this->service->create($course, $request->validated());
        return $this->created(__('messages.created'), new CourseLectureResource($lecture));
    }

    // PUT /courses/{course}/lectures/{lecture}
    public function update(Course $course, CourseLecture $lecture, CourseLectureRequest $request): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);
        $updated = $this->service->update($lecture, $request->validated());
        return $this->success(__('messages.updated'), new CourseLectureResource($updated));
    }

    // DELETE /courses/{course}/lectures/{lecture}
    public function destroy(Course $course, CourseLecture $lecture): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);
        $this->service->delete($lecture);
        return $this->deleted(__('messages.deleted'));
    }
}
