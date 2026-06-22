<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Requests\Api\Admin\AdminAnswerGradeRequest;
use App\Http\Requests\Api\Admin\AdminAssignmentStoreRequest;
use App\Http\Requests\Api\Admin\AdminAssignmentUpdateRequest;
use App\Http\Resources\Admin\AdminAssignmentListResource;
use App\Http\Resources\Admin\AdminAssignmentResource;
use App\Http\Resources\Admin\AdminAssignmentSubmissionDetailResource;
use App\Http\Resources\Admin\AdminAssignmentSubmissionResource;
use App\Models\CourseAssignment;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseAssignmentAnswer;
use App\Services\Admin\AdminAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin endpoints for the rich-question assignment workflow defined by the
 * 2026 Figma redesign. This controller is intentionally NEW and additive —
 * the legacy file-upload controller (CourseAssignmentController) remains
 * untouched for backward compatibility with the learner-facing API.
 */
class AdminAssignmentController extends ApiController
{
    public function __construct(private readonly AdminAssignmentService $service) {}

    /* ------------------------------------------------------------------ *
     | Assignments                                                        |
     * ------------------------------------------------------------------ */

    public function index(Request $request): JsonResponse
    {
        $assignments = $this->service->paginate(
            $request->integer('course_id') ?: null,
            $request->get('search'),
            $request->get('status'),
            (int) $request->get('per_page', 20),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminAssignmentListResource::collection($assignments),
        );
    }

    public function summary(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->summary());
    }

    public function listMinimal(Request $request): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->service->listMinimal($request->get('search'), (int) $request->get('limit', 200)),
        );
    }

    public function cohorts(Request $request): JsonResponse
    {
        $courseId = $request->integer('course_id');
        $cohorts = CourseSession::query()
            ->select(['id', 'course_id', 'title'])
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->orderBy('title')
            ->get();

        return $this->success(__('messages.retrieved'), $cohorts);
    }

    public function instructors(): JsonResponse
    {
        $instructors = User::query()
            ->select(['id', 'name'])
            ->whereHas('roles', fn ($q) => $q->whereIn('name', ['Admin', 'Instructor']))
            ->orderBy('name')
            ->limit(500)
            ->get();

        return $this->success(__('messages.retrieved'), $instructors);
    }

    public function show(int $id): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new AdminAssignmentResource($this->service->show($id)),
        );
    }

    public function store(AdminAssignmentStoreRequest $request): JsonResponse
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
        $user = $request->user();

        $assignment = $this->service->create($request->validated(), $user);

        return $this->created(__('messages.created'), new AdminAssignmentResource($assignment));
    }

    public function update(int $id, AdminAssignmentUpdateRequest $request): JsonResponse
    {
        $assignment = CourseAssignment::findOrFail($id);
        $updated    = $this->service->update($assignment, $request->validated());

        return $this->success(__('messages.updated'), new AdminAssignmentResource($updated));
    }

    public function destroy(int $id): JsonResponse
    {
        $assignment = CourseAssignment::findOrFail($id);
        $this->service->delete($assignment);

        return $this->deleted();
    }

    /* ------------------------------------------------------------------ *
     | Submissions                                                        |
     * ------------------------------------------------------------------ */

    public function submissions(Request $request): JsonResponse
    {
        $instructors = $request->input('instructor_ids');
        $learners    = $request->input('learner_ids');
        $courses     = $request->input('course_ids');

        $submissions = $this->service->paginateSubmissions(
            $request->integer('assignment_id') ?: null,
            $request->integer('course_id') ?: null,
            $request->integer('user_id') ?: null,
            is_array($instructors) ? array_map('intval', $instructors) : null,
            is_array($learners)    ? array_map('intval', $learners)    : null,
            is_array($courses)     ? array_map('intval', $courses)     : null,
            $request->get('status'),
            $request->get('search'),
            (int) $request->get('per_page', 20),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminAssignmentSubmissionResource::collection($submissions),
        );
    }

    public function showSubmission(int $id): JsonResponse
    {
        $submission = $this->service->showSubmission($id);

        return $this->success(
            __('messages.retrieved'),
            new AdminAssignmentSubmissionDetailResource($submission),
        );
    }

    public function gradeAnswer(int $submissionId, int $answerId, AdminAnswerGradeRequest $request): JsonResponse
    {
        $answer = UserCourseAssignmentAnswer::with('question')
            ->where('user_course_assignment_id', $submissionId)
            ->where('id', $answerId)
            ->firstOrFail();

        /** @var User|null $user */
        $user = $request->user();

        $graded = $this->service->gradeAnswer(
            $answer,
            (int) $request->input('awarded_score'),
            $request->input('feedback'),
            $user,
        );

        $submission = $this->service->showSubmission($submissionId);

        return $this->success(
            __('messages.updated'),
            [
                'answer'     => [
                    'id'             => $graded->id,
                    'awarded_score'  => (int) $graded->awarded_score,
                    'is_correct'     => $graded->is_correct,
                    'feedback'       => $graded->feedback,
                ],
                'submission' => new AdminAssignmentSubmissionDetailResource($submission),
            ],
        );
    }
}
