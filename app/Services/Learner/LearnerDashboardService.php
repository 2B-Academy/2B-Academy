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

        return $courses->map(function (Course $course) use ($request, $cohortIdsByCourse, $cohorts, $progressByCourse, $certificateStatusByCourse) {
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
            $card['module_progress_percent'] = $progressByCourse[$course->id] ?? 0;
            $card['certificate_status'] = $certificateStatusByCourse[$course->id] ?? null;

            return $card;
        })->values()->all();
    }
}
