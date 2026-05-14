<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseExamRequest;
use App\Http\Resources\CourseExamResource;
use App\Models\Course;
use App\Models\CourseExam;
use App\Services\CourseExamService;
use Illuminate\Http\JsonResponse;

class CourseExamController extends ApiController
{
    public function __construct(private readonly CourseExamService $service) {}

    // GET /courses/{course}/exams
    public function index(Course $course): JsonResponse
    {
        $exams = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseExamResource::collection($exams));
    }

    // GET /courses/{course}/exams/{exam}
    public function show(Course $course, CourseExam $exam): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $exam = $this->service->find($exam->id);
        return $this->success(__('messages.retrieved'), new CourseExamResource($exam));
    }

    // POST /courses/{course}/exams
    public function store(Course $course, CourseExamRequest $request): JsonResponse
    {
        $exam = $this->service->create($course, $request->validated());
        return $this->created(__('messages.created'), new CourseExamResource($exam));
    }

    // PUT /courses/{course}/exams/{exam}
    public function update(Course $course, CourseExam $exam, CourseExamRequest $request): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $updated = $this->service->update($exam, $request->validated());
        return $this->success(__('messages.updated'), new CourseExamResource($updated));
    }

    // DELETE /courses/{course}/exams/{exam}
    public function destroy(Course $course, CourseExam $exam): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $this->service->delete($exam);
        return $this->deleted(__('messages.deleted'));
    }
}
