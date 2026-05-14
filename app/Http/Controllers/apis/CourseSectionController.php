<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseSectionSyncRequest;
use App\Http\Resources\CourseSectionResource;
use App\Models\Course;
use App\Models\CourseSection;
use App\Services\CourseSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseSectionController extends ApiController
{
    public function __construct(private readonly CourseSectionService $service) {}

    // GET /courses/{course}/sections
    public function index(Course $course): JsonResponse
    {
        $sections = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseSectionResource::collection($sections));
    }

    // POST /courses/{course}/sections
    public function store(Course $course, Request $request): JsonResponse
    {
        $data = $request->validate([
            'name'    => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
        ]);
        $section = $this->service->create($course, $data);
        return $this->created(__('messages.created'), new CourseSectionResource($section));
    }

    // POST /courses/{course}/sections/sync  (bulk replace)
    public function sync(Course $course, CourseSectionSyncRequest $request): JsonResponse
    {
        $sections = $this->service->sync($course, $request->validated()['sections']);
        return $this->success(__('messages.updated'), CourseSectionResource::collection($sections));
    }

    // PUT /courses/{course}/sections/{section}
    public function update(Course $course, CourseSection $section, Request $request): JsonResponse
    {
        abort_if($section->course_id !== $course->id, 404);
        $data = $request->validate([
            'name'    => 'required|array',
            'name.ar' => 'required|string|max:255',
            'name.en' => 'nullable|string|max:255',
        ]);
        $updated = $this->service->update($section, $data);
        return $this->success(__('messages.updated'), new CourseSectionResource($updated));
    }

    // DELETE /courses/{course}/sections/{section}
    public function destroy(Course $course, CourseSection $section): JsonResponse
    {
        abort_if($section->course_id !== $course->id, 404);
        $this->service->delete($section);
        return $this->deleted(__('messages.deleted'));
    }
}
