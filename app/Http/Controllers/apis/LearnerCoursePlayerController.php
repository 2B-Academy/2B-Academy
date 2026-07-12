<?php

namespace App\Http\Controllers\apis;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Services\Learner\LearnerCoursePlayerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Composite "course player" workspace payload — the sidebar's week-grouped
 * lecture/quiz/assignment playlist, overall module progress, and the
 * persistent certificate-status badge. Read-only; the individual
 * lecture-progress and quiz/assignment-submission endpoints (routes/apis/my.php,
 * routes/apis/learner-assessments.php) remain the write paths.
 */
class LearnerCoursePlayerController extends ApiController
{
    public function __construct(private readonly LearnerCoursePlayerService $service) {}

    /** GET my/courses/{course}/outline */
    public function outline(Request $request, Course $course): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->service->outline($request->user(), $course),
        );
    }

    /** GET courses/{course}/lectures/{lecture} */
    public function lecture(Request $request, Course $course, CourseLecture $lecture): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);

        return $this->success(
            __('messages.retrieved'),
            $this->service->lecture($request->user(), $course, $lecture),
        );
    }
}
