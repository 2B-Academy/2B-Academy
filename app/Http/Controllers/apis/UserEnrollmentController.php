<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseEnrollmentRequest;
use App\Http\Resources\UsersCourseResource;
use App\Models\Course;
use App\Models\UsersCourse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserEnrollmentController extends ApiController
{
    public function index(Course $course, Request $request): JsonResponse
    {
        $enrollments = UsersCourse::with(['user:id,name,machine_code,department_name', 'group'])
            ->where('course_id', $course->id)
            ->when($request->get('group_id'), fn ($q, $id) => $q->where('group_id', $id))
            ->orderByDesc('id')
            ->paginate((int) $request->get('per_page', 20));

        return $this->paginated(__('messages.retrieved'), $enrollments);
    }

    public function store(Course $course, CourseEnrollmentRequest $request): JsonResponse
    {
        $data = [];
        foreach ($request->validated()['user_ids'] as $userId) {
            $data[$userId] = ['group_id' => $request->validated()['group_id']];
        }

        $course->users()->syncWithoutDetaching($data);

        return $this->success(__('messages.created'), [
            'enrolled' => count($data),
        ]);
    }

    public function destroy(Course $course, UsersCourse $enrollment): JsonResponse
    {
        abort_if($enrollment->course_id !== $course->id, 404);
        $enrollment->delete();

        return $this->deleted(__('messages.deleted'));
    }
}
