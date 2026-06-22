<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\Course;
use App\Models\User;
use App\Repositories\Contracts\Mobile\MyLearningRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * Compose the S-05 view-models. Pure orchestration over the repo +
 * MobileSettings — never queries the DB directly.
 */
final class MyLearningService
{
    public function __construct(
        private readonly MyLearningRepositoryInterface $repository,
        private readonly MobileSettings $settings,
    ) {}

    public function activeCoursesPaginated(User $user): LengthAwarePaginator
    {
        return $this->repository->activeCoursesForUser(
            $user,
            $this->settings->myLearningActivePerPage(),
        );
    }

    public function activeCoursesPreview(User $user): EloquentCollection
    {
        return $this->repository->previewActiveCourses(
            $user,
            $this->settings->myLearningActivePreviewCount(),
        );
    }

    public function progressFor(User $user, Course $course, int $cohortId, string $locale): array
    {
        return $this->repository->courseProgressSummary(
            $user,
            $course->id,
            $cohortId,
            $locale,
        );
    }

    public function sessionsAttendance(User $user, int $courseId, int $cohortId): Collection
    {
        return $this->repository->sessionsAttendance($user, $courseId, $cohortId);
    }

    public function userRatingForCourse(User $user, int $courseId): ?object
    {
        return $this->repository->userRatingForCourse($user, $courseId);
    }

    /**
     * Sequence number + localized label of the session that is up next
     * for the cohort, or null when there's nothing upcoming.
     *
     * @return array{number: int, name: string}|null
     */
    public function nextSessionFor(User $user, int $courseId, int $cohortId, string $locale): ?array
    {
        return $this->repository->nextSessionFor($user, $courseId, $cohortId, $locale);
    }

    /**
     * Live-session detection driver for the S-05 card. Returns null
     * if no session is happening now, otherwise the smallest
     * relevant session row.
     *
     * The detection re-uses the same buffers as the passcode flow so
     * "Live Now" and "Mark Present" stay in lock-step.
     */
    public function liveSessionFor(User $user, int $courseId, int $cohortId, Carbon $now): ?object
    {
        $openBuf  = $this->settings->attendanceSessionOpenBufferMinutes();
        $graceBuf = $this->settings->attendanceSessionGraceMinutes();

        return \DB::table('course_sessions')
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->where(function ($q) use ($now) {
                $q->whereNull('session_date')
                  ->orWhereDate('session_date', $now->toDateString());
            })
            ->where(function ($q) use ($now, $openBuf, $graceBuf) {
                $q->where(function ($q2) {
                    $q2->whereNull('time_from')->whereNull('time_to');
                })->orWhere(function ($q2) use ($now, $openBuf, $graceBuf) {
                    // `time_from`/`time_to` are TIME columns; TIMESTAMPDIFF on
                    // time-only values yields NULL, so compare on the clock
                    // directly. Open buffer = how early the window opens before
                    // start; grace = how late it stays open after the end.
                    $q2->whereRaw(
                        'time_from <= ADDTIME(?, SEC_TO_TIME(? * 60))',
                        [$now->format('H:i:s'), $openBuf],
                    )->whereRaw(
                        'time_to >= SUBTIME(?, SEC_TO_TIME(? * 60))',
                        [$now->format('H:i:s'), $graceBuf],
                    );
                });
            })
            ->orderBy('time_from')
            ->orderBy('id')
            ->first();
    }
}
