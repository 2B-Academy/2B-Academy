<?php

namespace App\Services\Learner;

use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\CourseAssignmentQuestion;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseAssignmentAnswer;
use App\Services\Grading\QuestionAnswerGrader;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Learner-facing submission flow for question-based `course_assignments`
 * (mcq/yes_no/open/reorder — see CourseAssignmentQuestion::TYPES).
 *
 * Mirrors LearnerQuizService's shape exactly, reusing the exact same
 * QuestionAnswerGrader so grading rules are never forked between the quiz
 * and assignment features. Purely additive: the legacy file-upload flow
 * (CourseAssignmentController::submit / CourseAssignmentService::submitFile)
 * is untouched and keeps serving assignments authored without questions.
 */
class LearnerAssignmentService
{
    public function __construct(private readonly QuestionAnswerGrader $grader) {}

    /** An assignment is "question-based" once it has an authored question bank. */
    public function isQuestionBased(CourseAssignment $assignment): bool
    {
        return CourseAssignmentQuestion::where('course_assignment_id', $assignment->id)->exists();
    }

    public function startOrResume(User $user, Course $course, CourseAssignment $assignment): array
    {
        $submission = $this->findOrCreateAttempt($user, $assignment);

        $questions = $assignment->questions()->get();
        $answers = UserCourseAssignmentAnswer::where('user_course_assignment_id', $submission->id)
            ->whereIn('course_assignment_question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('course_assignment_question_id');

        $locale = app()->getLocale();

        $questionPayload = $questions->map(function (CourseAssignmentQuestion $question) use ($answers, $locale) {
            $answer = $answers->get($question->id);

            return [
                'id' => $question->id,
                'position' => $question->position,
                'type' => $question->type,
                'score' => $question->score,
                'question' => $locale === 'ar' ? ($question->question_ar ?? $question->question_en) : ($question->question_en ?? $question->question_ar),
                'options' => $locale === 'ar' ? ($question->options_ar ?? $question->options_en) : ($question->options_en ?? $question->options_ar),
                'my_answer' => $answer?->answer,
                'is_answered' => $answer !== null,
            ];
        })->values()->all();

        $resumeQuestionId = collect($questionPayload)->firstWhere('is_answered', false)['id'] ?? null;

        return [
            'assignment' => $this->assignmentMeta($assignment, $questions->count(), $answers->count()),
            'submission_id' => $submission->id,
            'submission_status' => $submission->submitted_at ? 'submitted' : 'pending',
            'resume_question_id' => $resumeQuestionId,
            'questions' => $questionPayload,
        ];
    }

    public function answerQuestion(User $user, Course $course, CourseAssignment $assignment, CourseAssignmentQuestion $question, array $payload): array
    {
        abort_if($question->course_assignment_id !== $assignment->id, 404, __('messages.assignment_question_not_in_assignment'));

        $submission = $this->findOrCreateAttempt($user, $assignment);

        if ($submission->submitted_at !== null) {
            throw new HttpException(409, __('messages.assignment_already_submitted'));
        }

        $graded = $this->grader->grade($question, $payload);
        $locale = app()->getLocale();

        DB::transaction(function () use ($submission, $question, $payload, $graded) {
            UserCourseAssignmentAnswer::updateOrCreate(
                ['user_course_assignment_id' => $submission->id, 'course_assignment_question_id' => $question->id],
                [
                    'answer' => $payload,
                    'awarded_score' => $graded['awarded_score'],
                    'is_correct' => $graded['is_correct'],
                ]
            );
        });

        $totals = $this->recalculateTotals($submission);
        $questionsCount = $assignment->questions()->count();
        $answeredCount = UserCourseAssignmentAnswer::where('user_course_assignment_id', $submission->id)->count();

        $result = [
            'question_id' => $question->id,
            'is_correct' => $graded['is_correct'],
            'pending' => $graded['pending'],
            'awarded_score' => $graded['awarded_score'],
            'max_score' => $question->score,
            'correct_answer' => $graded['pending'] ? null : $this->grader->correctAnswerForDisplay($question, $locale),
            'running_total_score' => $totals['total_score'],
            'assignment_max_score' => $totals['max_score'],
            'answered_count' => $answeredCount,
            'questions_count' => $questionsCount,
            'finalized' => false,
            'results' => null,
        ];

        if ($answeredCount >= $questionsCount && $questionsCount > 0) {
            $result['finalized'] = true;
            $result['results'] = $this->finish($user, $course, $assignment);
        }

        return $result;
    }

    public function finish(User $user, Course $course, CourseAssignment $assignment): array
    {
        $submission = $this->findOrCreateAttempt($user, $assignment);

        if ($submission->submitted_at === null) {
            $totals = $this->recalculateTotals($submission);

            $submission->update([
                'submitted_at' => now(),
            ]);
        }

        return $this->results($user, $course, $assignment);
    }

    public function results(User $user, Course $course, CourseAssignment $assignment): array
    {
        $submission = UserCourseAssignment::where('user_id', $user->id)
            ->where('course_assignment_id', $assignment->id)
            ->first();

        if (!$submission || $submission->submitted_at === null) {
            throw new HttpException(404, __('messages.assignment_not_submitted'));
        }

        $totals = $this->recalculateTotals($submission);
        $locale = app()->getLocale();

        $questions = $assignment->questions()->get();
        $answers = UserCourseAssignmentAnswer::where('user_course_assignment_id', $submission->id)
            ->whereIn('course_assignment_question_id', $questions->pluck('id'))
            ->get()
            ->keyBy('course_assignment_question_id');

        $answerBreakdown = $questions->map(function (CourseAssignmentQuestion $question) use ($answers, $locale) {
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
                'my_answer' => $answer?->answer,
                'correct_answer' => $pending ? null : $this->grader->correctAnswerForDisplay($question, $locale),
            ];
        })->values()->all();

        $percent = $totals['max_score'] > 0 ? (int) round(($totals['total_score'] / $totals['max_score']) * 100) : 0;

        return [
            'submission_id' => $submission->id,
            'submission_status' => 'submitted',
            'total_score' => $totals['total_score'],
            'max_score' => $totals['max_score'],
            'percent' => $percent,
            'pass_score' => $assignment->pass_score,
            'passed' => $assignment->pass_score !== null ? $totals['total_score'] >= $assignment->pass_score : null,
            'submitted_at' => $submission->submitted_at?->format('Y-m-d H:i:s'),
            'answers' => $answerBreakdown,
        ];
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL HELPERS                                                  |
     * ------------------------------------------------------------------ */

    private function findOrCreateAttempt(User $user, CourseAssignment $assignment): UserCourseAssignment
    {
        $submission = UserCourseAssignment::where('user_id', $user->id)
            ->where('course_assignment_id', $assignment->id)
            ->first();

        if ($submission) {
            return $submission;
        }

        return UserCourseAssignment::create([
            'user_id' => $user->id,
            'course_assignment_id' => $assignment->id,
            'max_score' => $assignment->total_score,
        ]);
    }

    /** @return array{total_score: int, max_score: int} */
    private function recalculateTotals(UserCourseAssignment $submission): array
    {
        $total = (int) UserCourseAssignmentAnswer::where('user_course_assignment_id', $submission->id)->sum('awarded_score');
        $max = (int) $submission->assignment()->value('total_score');

        $submission->update(['total_score' => $total, 'max_score' => $max]);

        return ['total_score' => $total, 'max_score' => $max];
    }

    private function assignmentMeta(CourseAssignment $assignment, int $questionsCount, int $answeredCount): array
    {
        $locale = app()->getLocale();

        return [
            'id' => $assignment->id,
            'title' => $locale === 'ar' ? ($assignment->title_ar ?? $assignment->title) : $assignment->title,
            'instructions' => $locale === 'ar' ? ($assignment->instructions_ar ?? $assignment->instructions_en) : ($assignment->instructions_en ?? $assignment->instructions_ar),
            'pass_score' => $assignment->pass_score,
            'total_score' => $assignment->total_score,
            'due_date' => $assignment->due_date?->format('Y-m-d'),
            'questions_count' => $questionsCount,
            'answered_count' => $answeredCount,
        ];
    }
}
