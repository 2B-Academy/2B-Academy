<?php

namespace App\Services\Learner;

use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UsersCourse;
use App\Services\CertificateProjectionService;
use App\Services\LectureProgressService;
use App\Services\UserDashboardService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Composite payload backing the Angular "My Learnings" dashboard: per-course
 * cohort schedule, delivery type, module completion %, and certificate
 * status — everything batch-resolved across the whole course list (never
 * per-row) to avoid N+1.
 *
 * New endpoint (GET my/learnings) rather than extending the existing
 * GET my/courses / CourseResource: CourseResource is shared with the mobile
 * app (App\Http\Controllers\Api\Mobile\MyLearningController and friends
 * consume it too), so adding these learner-web-only composite fields there
 * would leak dashboard-specific batch queries into every CourseResource
 * consumer. This new endpoint reuses CourseResource's field formatting
 * as a base (via toArray()) and layers the extra fields on top.
 */
class LearnerDashboardService
{
    public function __construct(
        private readonly UserDashboardService $dashboard,
        private readonly LectureProgressService $progress,
        private readonly CertificateProjectionService $certificates,
    ) {}

    public function myLearnings(User $user, Request $request): array
    {
        $courses = $this->dashboard->getMyCourses($user);
        $courseIds = $courses->pluck('id')->all();

        if (empty($courseIds)) {
            return [];
        }

        $cohortIdsByCourse = UsersCourse::where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('group_id')
            ->pluck('group_id', 'course_id');

        $cohorts = CourseSection::whereIn('id', $cohortIdsByCourse->values()->unique()->all())
            ->get()
            ->keyBy('id');

        $progressByCourse = $this->progress->getCourseProgressBatch($user->id, $courseIds);
        $certificateStatusByCourse = $this->certificates->projectForCourses($user, $courses);

        // Batch-resolve the active certificate id per course (one query, no
        // N+1) so the card can render a "Download Certificate" action instead
        // of "Continue Learning" once the course is done.
        $certificateIdByCourse = DB::table('user_certificates')
            ->where('user_id', $user->id)
            ->where('status', 'active')
            ->whereIn('course_id', $courseIds)
            ->get(['id', 'course_id'])
            ->mapWithKeys(fn ($r) => [(int) $r->course_id => (int) $r->id])
            ->all();

        return $courses->map(function (Course $course) use ($request, $cohortIdsByCourse, $cohorts, $progressByCourse, $certificateStatusByCourse, $certificateIdByCourse) {
            $cohortId = $cohortIdsByCourse->get($course->id);
            /** @var CourseSection|null $cohort */
            $cohort = $cohortId ? $cohorts->get($cohortId) : null;

            $card = (new CourseResource($course))->toArray($request);

            $card['delivery_type'] = $course->course_type;
            $card['cohort'] = [
                'session_count' => $cohort?->number_of_sessions ?? $course->number_of_sessions,
                'start_date' => $cohort?->start_date?->format('Y-m-d'),
                'end_date' => $cohort?->end_date?->format('Y-m-d'),
            ];
            $percent = (int) ($progressByCourse[$course->id] ?? 0);
            $card['module_progress_percent'] = $percent;
            $card['certificate_status'] = $certificateStatusByCourse[$course->id] ?? null;
            // Explicit completion + downloadable certificate id so the
            // frontend can swap "Continue Learning" for certificate actions.
            $card['certificate_id'] = $certificateIdByCourse[$course->id] ?? null;
            $card['completed'] = $percent >= 100 || $card['certificate_id'] !== null;

            return $card;
        })->values()->all();
    }
}
