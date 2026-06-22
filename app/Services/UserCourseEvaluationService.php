<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Evaluation;
use App\Models\EvaluationCategory;
use App\Models\User;
use App\Models\UserCourseEvaluation;
use Illuminate\Database\Eloquent\Collection;

class UserCourseEvaluationService
{
    public function __construct(
        private readonly CertificateService $certificates,
    ) {}

    public function hasEvaluated(int $userId, int $courseId): bool
    {
        return UserCourseEvaluation::where('user_id', $userId)
            ->where('course_id', $courseId)
            ->exists();
    }

    /** Return evaluation form: categories + questions for a course. */
    public function getForm(): Collection
    {
        return EvaluationCategory::with('evaluations')->get();
    }

    /**
     * Submit course evaluation.
     * Mirrors AuthControllers\EvaluationController::store().
     *
     * $questions format: [ evaluation_id => answer_value, ... ]
     */
    public function submit(User $user, Course $course, int $instructorId, array $questions): void
    {
        $instructor = $course->instructors()->find($instructorId);

        $firstRow = null;

        foreach ($questions as $evaluationId => $answer) {
            $evaluation = Evaluation::with('category')->find($evaluationId);
            if (!$evaluation) continue;

            $row = UserCourseEvaluation::create([
                'user_id'                   => $user->id,
                'user_machine_code'         => $user->machine_code,
                'user_department'           => $user->department_name,
                'course_id'                 => $course->id,
                'course_name'               => $course->title,
                'instructor_id'             => $instructorId,
                'instructor_name'           => $instructor?->name,
                'evaluation_category_id'    => $evaluation->category?->id,
                'evaluation_category_name'  => $evaluation->category?->name,
                'evaluation_id'             => $evaluation->id,
                'evaluation_title'          => $evaluation->title,
                'evaluation_type'           => match ($evaluation->type) {
                    'five'  => 5,
                    'ten'   => 10,
                    default => 0,
                },
                'answer'                    => $answer,
            ]);

            $firstRow ??= $row;
        }

        // Completing an evaluation-based course earns its certificate.
        // Eligibility (course.certificate && is_evaluate) + dedup are
        // enforced inside CertificateService.
        if ($firstRow !== null) {
            $firstRow->setRelation('course', $course);
            $firstRow->setRelation('user', $user);
            $this->certificates->issueFromEvaluation($firstRow);
        }
    }
}
