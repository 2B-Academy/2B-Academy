<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AssignmentReviewRequest;
use App\Http\Requests\Api\AssignmentSubmissionRequest;
use App\Http\Requests\Api\CourseAssignmentRequest;
use App\Http\Resources\CourseAssignmentResource;
use App\Http\Resources\UserCourseAssignmentResource;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\UserCourseAssignment;
use App\Services\CourseAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CourseAssignmentController extends ApiController
{
    public function __construct(private readonly CourseAssignmentService $service) {}

    // --- Admin: manage assignments for a course ---

    public function index(Course $course): JsonResponse
    {
        $assignments = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseAssignmentResource::collection($assignments));
    }

    public function store(Course $course, CourseAssignmentRequest $request): JsonResponse
    {
        $assignment = $this->service->create($course, $request->validated()['title'], $request->file('file'));
        return $this->created(__('messages.created'), new CourseAssignmentResource($assignment));
    }

    public function update(Course $course, CourseAssignment $assignment, CourseAssignmentRequest $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $assignment = $this->service->update($assignment, $request->validated()['title'], $request->file('file'));
        return $this->success(__('messages.updated'), new CourseAssignmentResource($assignment));
    }

    public function destroy(Course $course, CourseAssignment $assignment): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $this->service->delete($assignment);
        return $this->deleted(__('messages.deleted'));
    }

    // --- Admin: view and review user submissions ---

    public function submissions(Course $course, CourseAssignment $assignment, Request $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $submissions = $this->service->listSubmissions($assignment, (int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $submissions);
    }

    public function review(
        Course $course,
        CourseAssignment $assignment,
        UserCourseAssignment $submission,
        AssignmentReviewRequest $request
    ): JsonResponse {
        abort_if($assignment->course_id !== $course->id, 404);
        abort_if($submission->course_assignment_id !== $assignment->id, 404);

        $data       = $request->validated();
        $submission = $this->service->reviewSubmission($submission, $data['feedback'] ?? null, $data['score'] ?? null);
        $submission->load('user', 'assignment');

        return $this->success(__('messages.updated'), new UserCourseAssignmentResource($submission));
    }

    // --- User: submit assignment file ---

    public function submit(Course $course, CourseAssignment $assignment, AssignmentSubmissionRequest $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);

        /** @var \App\Models\User $user */
        $user       = $request->user();
        $submission = $this->service->submitFile($assignment, $user, $request->file('file'));
        $submission->load('assignment');

        return $this->success(__('messages.updated'), new UserCourseAssignmentResource($submission));
    }

    // --- User: view own submission ---

    public function mySubmission(Course $course, CourseAssignment $assignment, Request $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);

        /** @var \App\Models\User $user */
        $user       = $request->user();
        $submission = UserCourseAssignment::with('assignment')
            ->where('course_assignment_id', $assignment->id)
            ->where('user_id', $user->id)
            ->first();

        if (!$submission) {
            return $this->success(__('messages.retrieved'), null);
        }

        return $this->success(__('messages.retrieved'), new UserCourseAssignmentResource($submission));
    }
}
