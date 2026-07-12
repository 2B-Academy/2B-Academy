<?php

namespace App\Services\Learner;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UserExamAnswer;
use App\Services\CertificateService;
use App\Services\Grading\QuestionAnswerGrader;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Learner-facing submission flow for the 2026 rich-question Quiz workflow
 * (mcq/yes_no/open/reorder — see CourseExamQuestion::TYPES).
 *
 * This is purely additive, new surface. It never touches
 * `UserExamService`/`UserExamController::submit` (the legacy MCQ-only flow)
 * — a different `CourseExam` row backs each workflow, discriminated the
 * same way AdminQuizService already does (`question_en` populated = rich).
 *
 * Progress is persisted after EVERY question (not just at final submit) by
 * upserting one `UserExamAnswer` row per question — this is what lets a
 * learner resume mid-quiz after a disconnect.
 */
class LearnerQuizService
{
    public function __construct(
        private readonly QuestionAnswerGrader $grader,
        private readonly CertificateService $certificates,
    ) {}

    /** A quiz only qualifies for this flow once it has rich-authored questions. */
    public function isRichQuiz(CourseExam $quiz): bool
    {
        return CourseExamQuestion::where('course_exam_id', $quiz->id)
            ->whereNotNull('question_en')
            ->exists();
    }

    /**
     * Start a new attempt or resume an in-progress one. Throws 409 if the
     * learner already finalized a submission for this quiz.
     */
    public function startOrResume(User $user, Course $course, CourseExam $quiz): array
    {
        $submission = $this->findOrCreateAttempt($user, $course, $quiz);

        $questions = $quiz->richQuestions()->get();
        $answers = UserExamAnswer::where('user_exam_id', $submission->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $locale = app()->getLocale();

        $questionPayload = $questions->map(function (CourseExamQuestion $question) use ($answers, $locale) {
            $answer = $answers->get($question->id);

            return [
                'id' => $question->id,
                'position' => $question->position,
                'type' => $question->type,
                'score' => $question->score,
                'question' => $locale === 'ar' ? ($question->question_ar ?? $question->question_en) : ($question->question_en ?? $question->question_ar),
                'options' => $locale === 'ar' ? ($question->options_ar ?? $question->options_en) : ($question->options_en ?? $question->options_ar),
                'my_answer' => $answer?->answer_payload,
                'is_answered' => $answer !== null,
            ];
        })->values()->all();

        $resumeQuestionId = collect($questionPayload)->firstWhere('is_answered', false)['id'] ?? null;

        return [
            'quiz' => $this->quizMeta($quiz, $questions->count(), $answers->count()),
            'submission_id' => $submission->id,
            'submission_status' => $submission->submission_status,
            'resume_question_id' => $resumeQuestionId,
            'questions' => $questionPayload,
        ];
    }

    /**
     * Grade and persist a single question's answer, then report the
     * running total. Auto-finalizes the attempt when this was the last
     * unanswered question.
     */
    public function answerQuestion(User $user, Course $course, CourseExam $quiz, CourseExamQuestion $question, array $payload): array
    {
        abort_if($question->course_exam_id !== $quiz->id, 404, __('messages.quiz_question_not_in_quiz'));

        $submission = $this->findOrCreateAttempt($user, $course, $quiz);

        if ($submission->submission_status === UserExam::SUBMISSION_SUBMITTED) {
            throw new HttpException(409, __('messages.quiz_already_submitted'));
        }

        $graded = $this->grader->grade($question, $payload);
        $locale = app()->getLocale();

        DB::transaction(function () use ($submission, $question, $payload, $graded) {
            UserExamAnswer::updateOrCreate(
                ['user_exam_id' => $submission->id, 'question_id' => $question->id],
                [
                    'answer_payload' => $payload,
                    'awarded_score' => $graded['awarded_score'],
                    'is_correct' => $graded['is_correct'] ?? false,
                ]
            );
        });

        $totals = $this->recalculateTotals($submission);
        $questionsCount = $quiz->richQuestions()->count();
        $answeredCount = UserExamAnswer::where('user_exam_id', $submission->id)->count();

        $result = [
            'question_id' => $question->id,
            'is_correct' => $graded['is_correct'],
            'pending' => $graded['pending'],
            'awarded_score' => $graded['awarded_score'],
            'max_score' => $question->score,
            'correct_answer' => $graded['pending'] ? null : $this->grader->correctAnswerForDisplay($question, $locale),
            'running_total_score' => $totals['total_score'],
            'quiz_max_score' => $totals['max_score'],
            'answered_count' => $answeredCount,
            'questions_count' => $questionsCount,
            'finalized' => false,
            'results' => null,
        ];

        if ($answeredCount >= $questionsCount && $questionsCount > 0) {
            $result['finalized'] = true;
            $result['results'] = $this->finish($user, $course, $quiz);
        }

        return $result;
    }

    /** Explicitly finalize the attempt. Unanswered questions score 0. */
    public function finish(User $user, Course $course, CourseExam $quiz): array
    {
        $submission = $this->findOrCreateAttempt($user, $course, $quiz);

        if ($submission->submission_status !== UserExam::SUBMISSION_SUBMITTED) {
            $totals = $this->recalculateTotals($submission);
            $passed = $quiz->pass_score !== null ? $totals['total_score'] >= $quiz->pass_score : null;

            $submission->update([
                'submission_status' => UserExam::SUBMISSION_SUBMITTED,
                'submitted_at' => now(),
                'user_degree' => $totals['total_score'],
                // Legacy status column — kept in sync so CertificateService's
                // existing final-exam-pass rule keeps working unchanged for
                // rich quizzes flagged `is_final`, without forking that rule.
                'status' => $passed === false ? 'fail' : 'success',
            ]);

            if ($passed !== false) {
                $this->certificates->issueFromExam($submission->fresh(['exam', 'course']));
            }
        }

        return $this->results($user, $course, $quiz);
    }

    /** Results shape — safe to call repeatedly, reflects live grading state. */
    public function results(User $user, Course $course, CourseExam $quiz): array
    {
        $submission = UserExam::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('exam_id', $quiz->id)
            ->first();

        if (!$submission || $submission->submission_status !== UserExam::SUBMISSION_SUBMITTED) {
            throw new HttpException(404, __('messages.quiz_not_submitted'));
        }

        $totals = $this->recalculateTotals($submission);
        $locale = app()->getLocale();

        $questions = $quiz->richQuestions()->get();
        $answers = UserExamAnswer::where('user_exam_id', $submission->id)
            ->whereIn('question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('question_id');

        $answerBreakdown = $questions->map(function (CourseExamQuestion $question) use ($answers, $locale) {
            $answer = $answers->get($question->id);
            $pending = $question->type === 'open' && ($answer === null || $answer->awarded_score === null);

            return [
                'question_id' => $question->id,
                'position' => $question->position,
                'type' => $question->type,
                'question' => $locale === 'ar' ? ($question->question_ar ?? $question->question_en) : ($question->question_en ?? $question->question_ar),
                'score' => $question->score,
                'awarded_score' => $answer?->awarded_score,
                'state' => $pending ? 'pending' : (($answer?->is_correct ?? false) ? 'correct' : 'incorrect'),
                'my_answer' => $answer?->answer_payload,
                'correct_answer' => $pending ? null : $this->grader->correctAnswerForDisplay($question, $locale),
            ];
        })->values()->all();

        $percent = $totals['max_score'] > 0 ? (int) round(($totals['total_score'] / $totals['max_score']) * 100) : 0;

        return [
            'submission_id' => $submission->id,
            'submission_status' => $submission->submission_status,
            'total_score' => $totals['total_score'],
            'max_score' => $totals['max_score'],
            'percent' => $percent,
            'pass_score' => $quiz->pass_score,
            'passed' => $quiz->pass_score !== null ? $totals['total_score'] >= $quiz->pass_score : null,
            'submitted_at' => $submission->submitted_at?->format('Y-m-d H:i:s'),
            'answers' => $answerBreakdown,
        ];
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL HELPERS                                                  |
     * ------------------------------------------------------------------ */

    private function findOrCreateAttempt(User $user, Course $course, CourseExam $quiz): UserExam
    {
        $submission = UserExam::where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->where('exam_id', $quiz->id)
            ->first();

        if ($submission) {
            return $submission;
        }

        return UserExam::create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'exam_id' => $quiz->id,
            'submission_status' => UserExam::SUBMISSION_PENDING,
            'max_score' => $quiz->total_score,
        ]);
    }

    /** @return array{total_score: int, max_score: int} */
    private function recalculateTotals(UserExam $submission): array
    {
        $total = (int) UserExamAnswer::where('user_exam_id', $submission->id)->sum('awarded_score');
        $max = (int) $submission->exam()->value('total_score');

        $submission->update(['total_score' => $total, 'max_score' => $max]);

        return ['total_score' => $total, 'max_score' => $max];
    }

    private function quizMeta(CourseExam $quiz, int $questionsCount, int $answeredCount): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $quiz->id,
            'title' => $quiz->getTranslation('title', $locale),
            'instructions' => $locale === 'ar' ? ($quiz->instructions_ar ?? $quiz->instructions_en) : ($quiz->instructions_en ?? $quiz->instructions_ar),
            'pass_score' => $quiz->pass_score,
            'total_score' => $quiz->total_score,
            'due_date' => $quiz->due_date?->format('Y-m-d'),
            'questions_count' => $questionsCount,
            'answered_count' => $answeredCount,
        ];
    }
}
