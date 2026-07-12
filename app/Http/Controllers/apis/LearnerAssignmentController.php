<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\LearnerAssignmentAnswerRequest;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseAssignmentQuestion;
use App\Services\Learner\LearnerAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Learner-facing submission API for question-based `course_assignments`.
 * Purely additive — the legacy file-upload endpoint
 * (POST courses/{course}/assignments/{assignment}/submit,
 * CourseAssignmentController::submit) is untouched and keeps serving
 * assignments authored as a plain file upload (no question bank).
 */
class LearnerAssignmentController extends ApiController
{
    public function __construct(private readonly LearnerAssignmentService $service) {}

    /** GET courses/{course}/assignments/{assignment}/take */
    public function take(Request $request, Course $course, CourseAssignment $assignment): JsonResponse
    {
        $this->guard($course, $assignment);

        return $this->success(
            __('messages.retrieved'),
            $this->service->startOrResume($request->user(), $course, $assignment),
        );
    }

    /** POST courses/{course}/assignments/{assignment}/questions/{question}/answer */
    public function answerQuestion(
        LearnerAssignmentAnswerRequest $request,
        Course $course,
        CourseAssignment $assignment,
        CourseAssignmentQuestion $question,
    ): JsonResponse {
        $this->guard($course, $assignment);

        $payload = $request->has('order')
            ? ['order' => $request->validated('order')]
            : ['value' => $request->validated('value')];

        return $this->success(
            __('messages.updated'),
            $this->service->answerQuestion($request->user(), $course, $assignment, $question, $payload),
        );
    }

    /** POST courses/{course}/assignments/{assignment}/finish */
    public function finish(Request $request, Course $course, CourseAssignment $assignment): JsonResponse
    {
        $this->guard($course, $assignment);

        return $this->success(
            __('messages.updated'),
            $this->service->finish($request->user(), $course, $assignment),
        );
    }

    /** GET courses/{course}/assignments/{assignment}/results */
    public function results(Request $request, Course $course, CourseAssignment $assignment): JsonResponse
    {
        $this->guard($course, $assignment);

        return $this->success(
            __('messages.retrieved'),
            $this->service->results($request->user(), $course, $assignment),
        );
    }

    private function guard(Course $course, CourseAssignment $assignment): void
    {
        abort_if($assignment->course_id !== $course->id, 404);
        abort_unless($this->service->isQuestionBased($assignment), 422, __('messages.assignment_not_question_based'));
    }
}
