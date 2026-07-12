<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\LearnerQuizAnswerRequest;
use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Services\Learner\LearnerQuizService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Learner-facing submission API for the 2026 rich-question Quiz workflow.
 * Purely additive — the legacy MCQ-only endpoint
 * (POST courses/{course}/exams/{exam}/submit, UserExamController::submit)
 * is untouched and keeps serving existing consumers.
 */
class LearnerQuizController extends ApiController
{
    public function __construct(private readonly LearnerQuizService $service) {}

    /** GET courses/{course}/quizzes/{quiz}/take */
    public function take(Request $request, Course $course, CourseExam $quiz): JsonResponse
    {
        $this->guard($course, $quiz);

        return $this->success(
            __('messages.retrieved'),
            $this->service->startOrResume($request->user(), $course, $quiz),
        );
    }

    /** POST courses/{course}/quizzes/{quiz}/questions/{question}/answer */
    public function answerQuestion(
        LearnerQuizAnswerRequest $request,
        Course $course,
        CourseExam $quiz,
        CourseExamQuestion $question,
    ): JsonResponse {
        $this->guard($course, $quiz);

        $payload = $request->has('order')
            ? ['order' => $request->validated('order')]
            : ['value' => $request->validated('value')];

        return $this->success(
            __('messages.updated'),
            $this->service->answerQuestion($request->user(), $course, $quiz, $question, $payload),
        );
    }

    /** POST courses/{course}/quizzes/{quiz}/finish */
    public function finish(Request $request, Course $course, CourseExam $quiz): JsonResponse
    {
        $this->guard($course, $quiz);

        return $this->success(
            __('messages.updated'),
            $this->service->finish($request->user(), $course, $quiz),
        );
    }

    /** GET courses/{course}/quizzes/{quiz}/results */
    public function results(Request $request, Course $course, CourseExam $quiz): JsonResponse
    {
        $this->guard($course, $quiz);

        return $this->success(
            __('messages.retrieved'),
            $this->service->results($request->user(), $course, $quiz),
        );
    }

    private function guard(Course $course, CourseExam $quiz): void
    {
        abort_if($quiz->course_id !== $course->id, 404, __('messages.quiz_not_found_for_course'));
        abort_unless($this->service->isRichQuiz($quiz), 404, __('messages.quiz_not_found_for_course'));
    }
}
