<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseSessionRequest;
use App\Http\Resources\CourseSessionResource;
use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseSessionController extends ApiController
{
    public function index(Course $course, Request $request): JsonResponse
    {
        $sessions = CourseSession::with('section')
            ->where('course_id', $course->id)
            ->when($request->get('section_id'), fn ($q, $id) => $q->where('section_id', $id))
            ->orderBy('session_date')
            ->orderBy('time_from')
            ->paginate((int) $request->get('per_page', 20));

        return $this->paginated(__('messages.retrieved'), $sessions);
    }

    public function store(Course $course, CourseSessionRequest $request): JsonResponse
    {
        $session = CourseSession::create(array_merge(
            $request->validated(),
            ['course_id' => $course->id]
        ));
        $session->load('section');

        return $this->created(__('messages.created'), new CourseSessionResource($session));
    }

    public function update(Course $course, CourseSession $session, CourseSessionRequest $request): JsonResponse
    {
        abort_if($session->course_id !== $course->id, 404);
        $session->update($request->validated());
        $session->load('section');

        return $this->success(__('messages.updated'), new CourseSessionResource($session));
    }

    public function destroy(Course $course, CourseSession $session): JsonResponse
    {
        abort_if($session->course_id !== $course->id, 404);
        $session->delete();

        return $this->deleted(__('messages.deleted'));
    }
}
