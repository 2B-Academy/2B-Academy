<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Compute per-user qualification progress for the S-05 "My
 * Qualifications" section.
 *
 * Wiring:
 *
 *   user
 *     └── job_title_id (users.job_title_id)
 *           └── job_titles
 *                 └── qualificationSkills() pivot `job_title_qualification_skill`
 *                       └── qualification_skills  ← required for the role
 *                             └── courses() pivot `course_qualification_skills`
 *                                   └── courses  ← courses that grant this qualification
 *
 *   user has completed a course iff EITHER:
 *     - 100% lecture progress  (user_lecture_progress.completed for every course_lectures row)
 *     - or the user earned a certificate for the course
 *         (UserExam(is_final, status=success, course.certificate) OR
 *          UserCourseEvaluation(course.is_evaluate))
 *
 *   percent = completed_courses / total_courses_granting_qualification
 *
 * No completion data ever lives on the user side — it's derived live.
 */
final class QualificationProgressService
{
    /**
     * Return all qualifications required for the user's role, with
     * per-qualification progress percent + course counts.
     *
     * @return Collection<int, array{
     *     id: int,
     *     name: string,
     *     total_courses: int,
     *     completed_courses: int,
     *     percent: int,
     * }>
     */
    public function forUser(User $user, string $locale): Collection
    {
        if (empty($user->job_title_id)) {
            return collect();
        }

        // Step 1: load the qualifications attached to the user's job
        // title (via job_title_qualification_skill).
        $qualifications = DB::table('qualification_skills as qs')
            ->join('job_title_qualification_skill as jtqs', 'jtqs.qualification_skill_id', '=', 'qs.id')
            ->where('jtqs.job_title_id', $user->job_title_id)
            ->select(['qs.id', 'qs.name'])
            ->orderBy('qs.id')
            ->get();

        if ($qualifications->isEmpty()) {
            return collect();
        }

        // Step 2: for each qualification, count courses granting it
        // and how many of those the user has completed.
        $qualificationIds = $qualifications->pluck('id')->all();

        $courseCounts = DB::table('course_qualification_skills')
            ->whereIn('qualification_skill_id', $qualificationIds)
            ->selectRaw('qualification_skill_id, COUNT(DISTINCT course_id) AS total_courses')
            ->groupBy('qualification_skill_id')
            ->pluck('total_courses', 'qualification_skill_id');

        $completedByQualification = $this->countCompletedCoursesByQualification(
            $user,
            $qualificationIds,
        );

        return $qualifications->map(function ($row) use ($locale, $courseCounts, $completedByQualification) {
            $total     = (int) ($courseCounts[$row->id] ?? 0);
            $completed = (int) ($completedByQualification[$row->id] ?? 0);
            $percent   = $total > 0
                ? (int) floor(($completed * 100) / $total)
                : 0;

            $decoded = json_decode((string) $row->name, true);
            $name    = is_array($decoded)
                ? ($decoded[$locale] ?? ($decoded['en'] ?? ($decoded['ar'] ?? '')))
                : (string) $row->name;

            return [
                'id'                => (int) $row->id,
                'name'              => (string) $name,
                'total_courses'     => $total,
                'completed_courses' => $completed,
                'percent'           => $percent,
            ];
        })->values();
    }

    /**
     * Top-N preview for S-05.
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function preview(User $user, string $locale, int $limit): Collection
    {
        return $this->forUser($user, $locale)
            ->sortByDesc(fn (array $r) => $r['percent']) // most-progressed first
            ->take($limit)
            ->values();
    }

    /**
     * Count, per qualification, how many courses the user has
     * completed. A course counts as completed iff EITHER (a) every
     * course_lectures row has a matching completed user_lecture_progress,
     * OR (b) the user has earned a certificate for the course
     * (final exam pass / submitted evaluation).
     *
     * @param  array<int, int> $qualificationIds
     * @return Collection<int, int>  qualification_id => completed_courses
     */
    private function countCompletedCoursesByQualification(User $user, array $qualificationIds): Collection
    {
        $userId = (int) $user->id;

        $completedCourseIds = $this->completedCourseIdsForUser($userId);

        if ($completedCourseIds->isEmpty()) {
            return collect();
        }

        return DB::table('course_qualification_skills')
            ->whereIn('qualification_skill_id', $qualificationIds)
            ->whereIn('course_id', $completedCourseIds->all())
            ->selectRaw('qualification_skill_id, COUNT(DISTINCT course_id) AS completed_count')
            ->groupBy('qualification_skill_id')
            ->pluck('completed_count', 'qualification_skill_id');
    }

    /**
     * @return Collection<int, int>  course ids
     */
    private function completedCourseIdsForUser(int $userId): Collection
    {
        // (a) full-completion via lecture progress.
        $fullyByProgress = DB::table('course_lectures')
            ->leftJoin('user_lecture_progress', function ($join) use ($userId) {
                $join->on('user_lecture_progress.lecture_id', '=', 'course_lectures.id')
                     ->where('user_lecture_progress.user_id', '=', $userId)
                     ->where('user_lecture_progress.completed', '=', true);
            })
            ->selectRaw('course_lectures.course_id, '
                .'COUNT(course_lectures.id) AS total_lectures, '
                .'COUNT(user_lecture_progress.id) AS completed_lectures')
            ->groupBy('course_lectures.course_id')
            ->havingRaw('completed_lectures > 0 AND completed_lectures >= total_lectures')
            ->pluck('course_id');

        // (b) certificate-issued via final exam.
        $byExam = DB::table('user_exams')
            ->join('courses', 'courses.id', '=', 'user_exams.course_id')
            ->join('course_exams', 'course_exams.id', '=', 'user_exams.exam_id')
            ->where('user_exams.user_id', $userId)
            ->where('user_exams.status', 'success')
            ->where('course_exams.is_final', true)
            ->where('courses.certificate', true)
            ->where('courses.is_evaluate', false)
            ->pluck('user_exams.course_id');

        // (c) certificate-issued via submitted evaluation.
        $byEvaluation = DB::table('user_course_evaluations')
            ->join('courses', 'courses.id', '=', 'user_course_evaluations.course_id')
            ->where('user_course_evaluations.user_id', $userId)
            ->where('courses.is_evaluate', true)
            ->pluck('user_course_evaluations.course_id');

        return $fullyByProgress
            ->merge($byExam)
            ->merge($byEvaluation)
            ->unique()
            ->values();
    }
}
