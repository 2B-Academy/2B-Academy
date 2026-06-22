<?php

namespace App\Http\Controllers\apis\Admin;

use App\Http\Controllers\apis\ApiController;
use App\Http\Requests\Api\Admin\AdminQuizAnswerGradeRequest;
use App\Http\Requests\Api\Admin\AdminQuizStoreRequest;
use App\Http\Requests\Api\Admin\AdminQuizUpdateRequest;
use App\Http\Resources\Admin\AdminQuizListResource;
use App\Http\Resources\Admin\AdminQuizResource;
use App\Http\Resources\Admin\AdminQuizSubmissionDetailResource;
use App\Http\Resources\Admin\AdminQuizSubmissionResource;
use App\Models\CourseExam;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UserExamAnswer;
use App\Services\Admin\AdminQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Admin endpoints for the rich-question Quiz workflow defined by the 2026
 * Figma redesign. The legacy `QuizController` (read-only learner attempts)
 * and the section-bound exam logic are intentionally NOT touched here.
 */
class AdminQuizController extends ApiController
{
    public function __construct(private readonly AdminQuizService $service) {}

    /* ------------------------------------------------------------------ *
     | Quizzes                                                            |
     * ------------------------------------------------------------------ */

    public function index(Request $request): JsonResponse
    {
        $quizzes = $this->service->paginate(
            $request->integer('course_id') ?: null,
            $request->get('search'),
            $request->get('status'),
            (int) $request->get('per_page', 20),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminQuizListResource::collection($quizzes),
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
            new AdminQuizResource($this->service->show($id)),
        );
    }

    public function store(AdminQuizStoreRequest $request): JsonResponse
    {
        /** @var \Illuminate\Contracts\Auth\Authenticatable|null $user */
        $user = $request->user();
        $quiz = $this->service->create($request->validated(), $user);

        return $this->created(__('messages.created'), new AdminQuizResource($quiz));
    }

    public function update(int $id, AdminQuizUpdateRequest $request): JsonResponse
    {
        $quiz    = CourseExam::findOrFail($id);
        $updated = $this->service->update($quiz, $request->validated());

        return $this->success(__('messages.updated'), new AdminQuizResource($updated));
    }

    public function destroy(int $id): JsonResponse
    {
        $quiz = CourseExam::findOrFail($id);
        $this->service->delete($quiz);

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
            $request->integer('quiz_id') ?: null,
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
            AdminQuizSubmissionResource::collection($submissions),
        );
    }

    public function showSubmission(int $id): JsonResponse
    {
        $submission = $this->service->showSubmission($id);

        return $this->success(
            __('messages.retrieved'),
            new AdminQuizSubmissionDetailResource($submission),
        );
    }

    public function gradeAnswer(int $submissionId, int $answerId, AdminQuizAnswerGradeRequest $request): JsonResponse
    {
        $answer = UserExamAnswer::with('question')
            ->where('user_exam_id', $submissionId)
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
                    'id'            => $graded->id,
                    'awarded_score' => (int) $graded->awarded_score,
                    'is_correct'    => $graded->is_correct,
                    'feedback'      => $graded->feedback,
                ],
                'submission' => new AdminQuizSubmissionDetailResource($submission),
            ],
        );
    }
}
