<?php

namespace App\Services\Admin;

use App\Models\CourseAssignment;
use App\Models\CourseAssignmentCohort;
use App\Models\CourseAssignmentQuestion;
use App\Models\User;
use App\Models\UserCourseAssignment;
use App\Models\UserCourseAssignmentAnswer;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

/**
 * Service backing the rich (question-based) admin assignment workflow.
 *
 * Note: this is a NEW service class. The legacy file-upload service
 * (App\Services\CourseAssignmentService) is left untouched.
 */
class AdminAssignmentService
{
    /* ------------------------------------------------------------------ *
     |  ASSIGNMENT CRUD                                                   |
     * ------------------------------------------------------------------ */

    public function paginate(
        ?int $courseId,
        ?string $search,
        ?string $status,
        int $perPage = 20
    ): LengthAwarePaginator {
        return CourseAssignment::query()
            ->with(['course:id,title', 'cohorts.session:id,title'])
            ->withCount('questions')
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($status, fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
            }))
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_assignment_questions')
                    ->whereColumn('course_assignment_questions.course_assignment_id', 'course_assignments.id');
            })
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function summary(): array
    {
        $assignments = CourseAssignment::query()
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_assignment_questions')
                    ->whereColumn('course_assignment_questions.course_assignment_id', 'course_assignments.id');
            });

        return [
            'assignments_count' => (clone $assignments)->count(),
            'courses_count'     => (clone $assignments)->distinct('course_id')->count('course_id'),
        ];
    }

    public function listMinimal(?string $search, int $limit = 200): array
    {
        return CourseAssignment::query()
            ->select(['id', 'title', 'title_ar', 'course_id', 'status'])
            ->with('course:id,title')
            ->whereExists(function ($sub) {
                $sub->select(DB::raw(1))
                    ->from('course_assignment_questions')
                    ->whereColumn('course_assignment_questions.course_assignment_id', 'course_assignments.id');
            })
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                      ->orWhere('title_ar', 'like', "%{$search}%");
            }))
            ->orderBy('title')
            ->limit($limit)
            ->get()
            ->map(fn ($a) => [
                'id'           => $a->id,
                'title'        => $a->title,
                'title_ar'     => $a->title_ar,
                'course_id'    => $a->course_id,
                'course_title' => optional($a->course)->title,
                'status'       => $a->status,
            ])
            ->all();
    }

    public function show(int $id): CourseAssignment
    {
        return CourseAssignment::query()
            ->with([
                'course:id,title',
                'creator:id,name',
                'questions',
                'cohorts.session:id,title',
            ])
            ->findOrFail($id);
    }

    public function create(array $data, ?Authenticatable $creator): CourseAssignment
    {
        return DB::transaction(function () use ($data, $creator) {
            $assignment = CourseAssignment::query()->create([
                'course_id'       => $data['course_id'],
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
            ]);

            $this->syncQuestions($assignment, $data['questions'] ?? []);
            $this->syncCohorts($assignment, $data['cohort_scope'], $data['cohort_ids'] ?? []);

            return $this->show($assignment->id);
        });
    }

    public function update(CourseAssignment $assignment, array $data): CourseAssignment
    {
        return DB::transaction(function () use ($assignment, $data) {
            $assignment->update([
                'course_id'       => $data['course_id'],
                'title'           => $data['title'],
                'title_ar'        => $data['title_ar'] ?? null,
                'instructions_en' => $data['instructions_en'] ?? null,
                'instructions_ar' => $data['instructions_ar'] ?? null,
                'due_date'        => $data['due_date'] ?? null,
                'cohort_scope'    => $data['cohort_scope'],
                'pass_score'      => $data['pass_score'] ?? null,
                'status'          => $data['status'] ?? $assignment->status,
                'total_score'     => $this->sumQuestionScores($data['questions'] ?? []),
            ]);

            $this->syncQuestions($assignment, $data['questions'] ?? []);
            $this->syncCohorts($assignment, $data['cohort_scope'], $data['cohort_ids'] ?? []);

            return $this->show($assignment->id);
        });
    }

    public function delete(CourseAssignment $assignment): void
    {
        DB::transaction(function () use ($assignment) {
            CourseAssignmentQuestion::where('course_assignment_id', $assignment->id)->delete();
            CourseAssignmentCohort::where('course_assignment_id', $assignment->id)->delete();
            $assignment->delete();
        });
    }

    /* ------------------------------------------------------------------ *
     |  SUBMISSIONS                                                       |
     * ------------------------------------------------------------------ */

    public function paginateSubmissions(
        ?int $assignmentId,
        ?int $courseId,
        ?int $userId,
        ?array $instructorIds,
        ?array $learnerIds,
        ?array $courseIds,
        ?string $status,
        ?string $search,
        int $perPage = 20
    ): LengthAwarePaginator {
        return UserCourseAssignment::query()
            ->with([
                'user:id,name,machine_code,department_name',
                'assignment.course:id,title',
                'assignment.creator:id,name',
                'assignment.cohorts.session:id,title',
            ])
            ->whereHas('assignment', function ($q) {
                $q->whereExists(function ($sub) {
                    $sub->select(DB::raw(1))
                        ->from('course_assignment_questions')
                        ->whereColumn('course_assignment_questions.course_assignment_id', 'course_assignments.id');
                });
            })
            ->when($assignmentId, fn ($q) => $q->where('course_assignment_id', $assignmentId))
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->when($courseId, fn ($q) => $q->whereHas('assignment', fn ($inner) => $inner->where('course_id', $courseId)))
            ->when(!empty($instructorIds), fn ($q) => $q->whereHas('assignment', fn ($inner) => $inner->whereIn('created_by', $instructorIds)))
            ->when(!empty($learnerIds), fn ($q) => $q->whereIn('user_id', $learnerIds))
            ->when(!empty($courseIds), fn ($q) => $q->whereHas('assignment', fn ($inner) => $inner->whereIn('course_id', $courseIds)))
            ->when($status === 'graded', fn ($q) => $q->whereNotNull('total_score'))
            ->when($status === 'pending', fn ($q) => $q->whereNull('total_score'))
            ->when($search, fn ($q) => $q->whereHas('user', fn ($inner) => $inner->where('name', 'like', "%{$search}%")))
            ->latest('id')
            ->paginate($perPage);
    }

    public function showSubmission(int $id): UserCourseAssignment
    {
        return UserCourseAssignment::query()
            ->with([
                'user:id,name,machine_code,department_name',
                'assignment.course:id,title',
                'assignment.creator:id,name',
                'answers.question',
            ])
            ->findOrFail($id);
    }

    public function gradeAnswer(
        UserCourseAssignmentAnswer $answer,
        int $awardedScore,
        ?string $feedback,
        ?User $reviewer
    ): UserCourseAssignmentAnswer {
        return DB::transaction(function () use ($answer, $awardedScore, $feedback, $reviewer) {
            $maxScore = (int) ($answer->question->score ?? 0);
            $awarded  = max(0, min($awardedScore, $maxScore));

            $answer->update([
                'awarded_score' => $awarded,
                'feedback'      => $feedback,
                'is_correct'    => $maxScore > 0 ? $awarded === $maxScore : null,
            ]);

            $this->recalculateSubmissionTotals($answer->user_course_assignment_id, $reviewer?->id);

            return $answer->fresh(['question']);
        });
    }

    /* ------------------------------------------------------------------ *
     |  INTERNAL HELPERS                                                  |
     * ------------------------------------------------------------------ */

    private function syncQuestions(CourseAssignment $assignment, array $questions): void
    {
        CourseAssignmentQuestion::where('course_assignment_id', $assignment->id)->delete();

        foreach (array_values($questions) as $index => $q) {
            CourseAssignmentQuestion::create([
                'course_assignment_id' => $assignment->id,
                'position'             => $index,
                'type'                 => $q['type'],
                'score'                => (int) ($q['score'] ?? 0),
                'question_en'          => $q['question_en'],
                'question_ar'          => $q['question_ar'] ?? null,
                'options_en'           => $q['options_en'] ?? null,
                'options_ar'           => $q['options_ar'] ?? null,
                'correct_answer_en'    => $q['correct_answer_en'] ?? null,
                'correct_answer_ar'    => $q['correct_answer_ar'] ?? null,
                'explanation_en'       => $q['explanation_en'] ?? null,
                'explanation_ar'       => $q['explanation_ar'] ?? null,
            ]);
        }
    }

    private function syncCohorts(CourseAssignment $assignment, string $scope, array $cohortIds): void
    {
        CourseAssignmentCohort::where('course_assignment_id', $assignment->id)->delete();

        if ($scope !== 'specific') {
            return;
        }

        foreach (array_unique($cohortIds) as $sessionId) {
            CourseAssignmentCohort::create([
                'course_assignment_id' => $assignment->id,
                'course_session_id'    => $sessionId,
            ]);
        }
    }

    private function sumQuestionScores(array $questions): int
    {
        return array_reduce($questions, fn ($carry, $q) => $carry + (int) ($q['score'] ?? 0), 0);
    }

    private function recalculateSubmissionTotals(int $submissionId, ?int $reviewerId): void
    {
        $submission = UserCourseAssignment::with('answers')->find($submissionId);
        if (!$submission) {
            return;
        }

        $total = $submission->answers->sum(function ($a) {
            return (int) ($a->awarded_score ?? 0);
        });

        $submission->update([
            'total_score' => $total,
            'reviewed_at' => now(),
            'reviewed_by' => $reviewerId,
        ]);
    }
}
