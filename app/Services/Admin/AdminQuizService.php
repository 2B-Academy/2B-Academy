<?php

namespace App\Services\Admin;

use App\Models\CourseExam;
use App\Models\CourseExamCohort;
use App\Models\CourseExamQuestion;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UserExamAnswer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Service backing the rich (question-based) admin Quiz workflow.
 *
 * This service is purely additive. The legacy MCQ-only flow served by
 * `QuizController` and the section-bound exam logic continue to work
 * unchanged — they simply ignore the new columns / pivot table.
 */
class AdminQuizService
{
    /* ------------------------------------------------------------------ *
     |  QUIZ CRUD                                                         |
     * ------------------------------------------------------------------ */

    public function paginate(
        ?int $courseId,
        ?string $search,
        ?string $status,
        int $perPage = 20
    ): LengthAwarePaginator {
        return CourseExam::query()
            ->with(['course:id,title', 'cohorts.session:id,title'])
            ->withCount(['richQuestions as rich_questions_count'])
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_exam_questions')
                    ->whereColumn('course_exam_questions.course_exam_id', 'course_exams.id')
                    ->whereNotNull('course_exam_questions.question_en');
            })
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
            }))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function summary(): array
    {
        $quizzes = CourseExam::query()
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_exam_questions')
                    ->whereColumn('course_exam_questions.course_exam_id', 'course_exams.id')
                    ->whereNotNull('course_exam_questions.question_en');
            });

        return [
            'quizzes_count' => (clone $quizzes)->count(),
            'courses_count' => (clone $quizzes)->distinct('course_id')->count('course_id'),
        ];
    }

    public function listMinimal(?string $search, int $limit = 200): array
    {
        return CourseExam::query()
            ->select(['id', 'title', 'title_ar', 'course_id', 'status'])
            ->with('course:id,title')
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_exam_questions')
                    ->whereColumn('course_exam_questions.course_exam_id', 'course_exams.id')
                    ->whereNotNull('course_exam_questions.question_en');
            })
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
            }))
            ->orderBy('title')
            ->limit($limit)
            ->get()
            ->map(fn ($q) => [
                'id'           => $q->id,
                'title'        => $q->title,
                'title_ar'     => $q->title_ar,
                'course_id'    => $q->course_id,
                'course_title' => optional($q->course)->title,
                'status'       => $q->status,
            ])
            ->all();
    }

    public function show(int $id): CourseExam
    {
        return CourseExam::query()
            ->with([
                'course:id,title',
                'creator:id,name',
                'richQuestions',
                'cohorts.session:id,title',
            ])
            ->findOrFail($id);
    }

    public function create(array $data, ?Authenticatable $creator): CourseExam
    {
        return DB::transaction(function () use ($data, $creator) {
            /** @var CourseExam $quiz */
            $quiz = CourseExam::query()->create([
                'course_id'       => $data['course_id'],
                'section_id'      => null,
                'title'           => $data['title'],
                'title_ar'        => $data['title_ar'] ?? null,
                'instructions_en' => $data['instructions_en'] ?? null,
                'instructions_ar' => $data['instructions_ar'] ?? null,
                'due_date'        => $data['due_date'] ?? null,
                'cohort_scope'    => $data['cohort_scope'],
                'pass_score'      => $data['pass_score'] ?? null,
                'status'          => $data['status'] ?? 'draft',
                'created_by'      => $creator?->id,
                'total_score'     => $this->sumQuestionScores($data['questions'] ?? []),
                // Legacy column requirements:
                'degree'          => $this->sumQuestionScores($data['questions'] ?? []),
                'duration'        => 60,
            ]);

            $this->syncQuestions($quiz, $data['questions'] ?? []);
            $this->syncCohorts($quiz, $data['cohort_scope'], $data['cohort_ids'] ?? []);

            return $this->show($quiz->id);
        });
    }

    public function update(CourseExam $quiz, array $data): CourseExam
    {
        return DB::transaction(function () use ($quiz, $data) {
            $total = $this->sumQuestionScores($data['questions'] ?? []);

            $quiz->update([
                'course_id'       => $data['course_id'],
                'title'           => $data['title'],
                'title_ar'        => $data['title_ar'] ?? null,
                'instructions_en' => $data['instructions_en'] ?? null,
                'instructions_ar' => $data['instructions_ar'] ?? null,
                'due_date'        => $data['due_date'] ?? null,
                'cohort_scope'    => $data['cohort_scope'],
                'pass_score'      => $data['pass_score'] ?? null,
                'status'          => $data['status'] ?? $quiz->status,
                'total_score'     => $total,
                'degree'          => $total,
            ]);

            $this->syncQuestions($quiz, $data['questions'] ?? []);
            $this->syncCohorts($quiz, $data['cohort_scope'], $data['cohort_ids'] ?? []);

            return $this->show($quiz->id);
        });
    }

    public function delete(CourseExam $quiz): void
    {
        DB::transaction(function () use ($quiz) {
            CourseExamCohort::where('course_exam_id', $quiz->id)->delete();
            // The legacy `course_exam_questions` rows + their `course_exam_question_answers`
            // children cascade via foreign keys when the parent quiz is removed.
            $quiz->delete();
        });
    }

    /* ------------------------------------------------------------------ *
     |  SUBMISSIONS                                                       |
     * ------------------------------------------------------------------ */

    public function paginateSubmissions(
        ?int $quizId,
        ?int $courseId,
        ?int $userId,
        ?array $instructorIds,
        ?array $learnerIds,
        ?array $courseIds,
        ?string $status,
        ?string $search,
        int $perPage = 20
    ): LengthAwarePaginator {
        return UserExam::query()
            ->with([
                'user:id,name',
                'exam.course:id,title',
                'exam.creator:id,name',
                'exam.cohorts.session:id,title',
            ])
            ->whereHas('exam', function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('course_exam_questions')
                        ->whereColumn('course_exam_questions.course_exam_id', 'course_exams.id')
                        ->whereNotNull('course_exam_questions.question_en');
                });
            })
            ->when($quizId, fn ($q) => $q->where('exam_id', $quizId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when(!empty($instructorIds), fn ($q) => $q->whereHas('exam', fn ($inner) => $inner->whereIn('created_by', $instructorIds)))
            ->when(!empty($learnerIds), fn ($q) => $q->whereIn('user_id', $learnerIds))
            ->when(!empty($courseIds), fn ($q) => $q->whereIn('course_id', $courseIds))
            ->when($status === 'graded', fn ($q) => $q->whereNotNull('total_score'))
            ->when($status === 'pending', fn ($q) => $q->whereNull('total_score'))
            ->when($search, fn ($q) => $q->whereHas('user', fn ($inner) => $inner->where('name', 'like', "%{$search}%")))
            ->latest('id')
            ->paginate($perPage);
    }

    public function showSubmission(int $id): UserExam
    {
        return UserExam::query()
            ->with([
                'user:id,name',
                'exam.course:id,title',
                'exam.creator:id,name',
                'answers.question',
            ])
            ->findOrFail($id);
    }

    public function gradeAnswer(
        UserExamAnswer $answer,
        int $awardedScore,
        ?string $feedback,
        ?User $reviewer
    ): UserExamAnswer {
        return DB::transaction(function () use ($answer, $awardedScore, $feedback, $reviewer) {
            $maxScore = (int) ($answer->question->score ?? 0);
            $awarded  = max(0, min($awardedScore, $maxScore));

            $answer->update([
                'awarded_score' => $awarded,
                'feedback'      => $feedback,
                'is_correct'    => $maxScore > 0 ? $awarded === $maxScore : null,
            ]);

            $this->recalculateSubmissionTotals($answer->user_exam_id, $reviewer?->id);

            return $answer->fresh(['question']);
        });
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL HELPERS                                                  |
     * ------------------------------------------------------------------ */

    private function syncQuestions(CourseExam $quiz, array $questions): void
    {
        CourseExamQuestion::where('course_exam_id', $quiz->id)->delete();

        foreach (array_values($questions) as $index => $q) {
            CourseExamQuestion::create([
                'course_exam_id'    => $quiz->id,
                'position'          => $index,
                'type'              => $q['type'],
                'score'             => (int) ($q['score'] ?? 0),
                'question'          => $q['question_en'], // legacy translatable mirror
                'question_en'       => $q['question_en'],
                'question_ar'       => $q['question_ar'] ?? null,
                'options_en'        => $q['options_en'] ?? null,
                'options_ar'        => $q['options_ar'] ?? null,
                'correct_answer_en' => $q['correct_answer_en'] ?? null,
                'correct_answer_ar' => $q['correct_answer_ar'] ?? null,
                'explanation_en'    => $q['explanation_en'] ?? null,
                'explanation_ar'    => $q['explanation_ar'] ?? null,
            ]);
        }
    }

    private function syncCohorts(CourseExam $quiz, string $scope, array $cohortIds): void
    {
        CourseExamCohort::where('course_exam_id', $quiz->id)->delete();

        if ($scope !== 'specific') {
            return;
        }

        foreach (array_unique($cohortIds) as $sessionId) {
            CourseExamCohort::create([
                'course_exam_id'    => $quiz->id,
                'course_session_id' => $sessionId,
            ]);
        }
    }

    private function sumQuestionScores(array $questions): int
    {
        return array_reduce($questions, fn ($carry, $q) => $carry + (int) ($q['score'] ?? 0), 0);
    }

    private function recalculateSubmissionTotals(int $submissionId, ?int $reviewerId): void
    {
        $submission = UserExam::with('answers')->find($submissionId);
        if (!$submission) {
            return;
        }

        $total = $submission->answers->sum(fn ($a) => (int) ($a->awarded_score ?? 0));

        $submission->update([
            'total_score' => $total,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);
    }
}
