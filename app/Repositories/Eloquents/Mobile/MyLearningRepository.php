<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents\Mobile;

use App\Models\Course;
use App\Models\CourseRating;
use App\Models\User;
use App\Repositories\Contracts\Mobile\MyLearningRepositoryInterface;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Eloquent implementation of the My Learning read surface (S-05).
 *
 * "Active" course = the user has a users_courses row, and the
 * associated cohort (course_sections.id = users_courses.group_id) is
 * not yet `completed`. Completion is read live from cohort dates so
 * the daily cron isn't required to keep the screen honest.
 *
 * All progress / attendance / absence numbers come from JOIN+COUNT —
 * never from a snapshot column.
 */
final class MyLearningRepository implements MyLearningRepositoryInterface
{
    public function __construct(private readonly Course $course) {}

    public function activeCoursesForUser(User $user, int $perPage): LengthAwarePaginator
    {
        return $this->activeCoursesQuery($user)->paginate($perPage);
    }

    public function previewActiveCourses(User $user, int $limit): EloquentCollection
    {
        return $this->activeCoursesQuery($user)->limit($limit)->get();
    }

    public function courseProgressSummary(User $user, int $courseId, int $cohortId, string $locale): array
    {
        // 1. Total lectures in the course (the catalogue truth — not
        //    snapshotted on the user side).
        $totalLectures = (int) DB::table('course_lectures')
            ->where('course_id', $courseId)
            ->count();

        // 2. The user's completed lectures for this course.
        $completedLectures = (int) DB::table('user_lecture_progress')
            ->join('course_lectures', 'course_lectures.id', '=', 'user_lecture_progress.lecture_id')
            ->where('user_lecture_progress.user_id', $user->id)
            ->where('course_lectures.course_id', $courseId)
            ->where('user_lecture_progress.completed', true)
            ->count();

        // 3. Sessions counts (total + past) and attendance count.
        $today = now()->toDateString();

        $totalSessions = (int) DB::table('course_sessions')
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->count();

        $pastSessions = (int) DB::table('course_sessions')
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->whereNotNull('session_date')
            ->whereDate('session_date', '<=', $today)
            ->count();

        $attended = (int) DB::table('attendances')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->count();

        // Absences are the number of *past* sessions for which the
        // user has no attendance row. We cap at 0 so manual / extra
        // attendance records don't underflow.
        $absences = max(0, $pastSessions - $attended);

        // 4. Next-up lecture for the "Next: …" hint on the card.
        //    The "next" lecture is the lowest-id lecture for this
        //    course that the user has NOT yet completed.
        $nextLectureRow = DB::table('course_lectures')
            ->leftJoin('user_lecture_progress', function ($join) use ($user) {
                $join->on('user_lecture_progress.lecture_id', '=', 'course_lectures.id')
                     ->where('user_lecture_progress.user_id', '=', $user->id);
            })
            ->where('course_lectures.course_id', $courseId)
            ->where(function ($q) {
                $q->whereNull('user_lecture_progress.completed')
                  ->orWhere('user_lecture_progress.completed', false);
            })
            ->orderBy('course_lectures.id')
            ->select(['course_lectures.title'])
            ->first();

        $nextTitle = null;
        if ($nextLectureRow !== null) {
            $decoded = json_decode((string) $nextLectureRow->title, true);
            if (is_array($decoded)) {
                $nextTitle = $decoded[$locale] ?? ($decoded['en'] ?? ($decoded['ar'] ?? null));
            } else {
                $nextTitle = $nextLectureRow->title;
            }
        }

        // 5. Progress percent — bounded [0, 100], guarded against
        //    division by zero so empty courses show "0%" instead of
        //    crashing.
        $progress = $totalLectures > 0
            ? (int) floor(($completedLectures * 100) / $totalLectures)
            : 0;

        return [
            'attended'           => $attended,
            'past_sessions'      => $pastSessions,
            'total_sessions'     => $totalSessions,
            'absences'           => $absences,
            'progress_percent'   => $progress,
            'completed_lectures' => $completedLectures,
            'total_lectures'     => $totalLectures,
            'next_unit_title'    => $nextTitle,
        ];
    }

    public function sessionsAttendance(User $user, int $courseId, int $cohortId): Collection
    {
        $rows = DB::table('course_sessions')
            ->leftJoin('attendances', function ($join) use ($user, $courseId, $cohortId) {
                $join->on('attendances.session_id', '=', 'course_sessions.id')
                     ->where('attendances.user_id', '=', $user->id)
                     ->where('attendances.course_id', '=', $courseId)
                     ->where('attendances.section_id', '=', $cohortId);
            })
            ->where('course_sessions.course_id', $courseId)
            ->where('course_sessions.section_id', $cohortId)
            ->orderBy('course_sessions.session_date')
            ->orderBy('course_sessions.time_from')
            ->select([
                'course_sessions.id',
                'course_sessions.title',
                'course_sessions.session_date',
                'course_sessions.time_from',
                'course_sessions.time_to',
                DB::raw('CASE WHEN attendances.id IS NULL THEN 0 ELSE 1 END AS attended'),
            ])
            ->get();

        return collect($rows)->map(fn ($row) => (object) [
            'id'           => (int) $row->id,
            'title'        => (string) $row->title,
            'session_date' => $row->session_date,
            'time_from'    => $row->time_from,
            'time_to'      => $row->time_to,
            'attended'     => (bool) $row->attended,
        ]);
    }

    public function userRatingForCourse(User $user, int $courseId): ?object
    {
        $row = CourseRating::query()
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->first(['id', 'rating', 'comment', 'created_at']);

        if ($row === null) {
            return null;
        }

        return (object) [
            'id'         => (int) $row->id,
            'rating'     => (int) $row->rating,
            'comment'    => $row->comment,
            'created_at' => $row->created_at,
        ];
    }

    public function nextSessionFor(User $user, int $courseId, int $cohortId, string $locale): ?array
    {
        $now = now();

        $sessions = DB::table('course_sessions')
            ->where('course_id', $courseId)
            ->where('section_id', $cohortId)
            ->get(['id', 'session_date', 'time_from', 'time_to']);

        // Auto-end length for sessions that have no explicit end time.
        // Prefers the cohort's stored "Avg. Session Time"
        // (course_sections.avg_session_time, in HOURS), then the average
        // computed from the cohort's own timed sessions, then 2h.
        $avgMinutes = $this->cohortAutoEndMinutes($cohortId, $sessions);

        // How many sessions are already finished (real end-time passed,
        // or auto-ended once the average length elapsed).
        $endedCount = 0;
        foreach ($sessions as $session) {
            if ($this->sessionHasEnded($session, $now, $avgMinutes)) {
                $endedCount++;
            }
        }

        // Sessions are 1-indexed and the learner is always heading toward
        // the one right after the last finished session. With zero
        // sessions (or none finished yet) this resolves to "Session 1".
        $number = $endedCount + 1;

        return [
            'number' => $number,
            'name'   => $locale === 'ar' ? "الجلسة {$number}" : "Session {$number}",
        ];
    }

    // ────────────────────────────────────────────────────────────
    // Internals
    // ────────────────────────────────────────────────────────────

    /**
     * Minutes after which an open (no `time_to`) session is auto-ended.
     * The cohort's admin-set "Avg. Session Time" (stored in hours) wins;
     * otherwise we average the cohort's own timed sessions.
     *
     * @param  \Illuminate\Support\Collection<int, object>  $sessions
     */
    private function cohortAutoEndMinutes(int $cohortId, Collection $sessions): int
    {
        $avgHours = DB::table('course_sections')
            ->where('id', $cohortId)
            ->value('avg_session_time');

        if ($avgHours !== null && (float) $avgHours > 0) {
            return (int) round((float) $avgHours * 60);
        }

        return $this->cohortAverageSessionMinutes($sessions);
    }

    /**
     * @param  \Illuminate\Support\Collection<int, object>  $sessions
     */
    private function cohortAverageSessionMinutes(Collection $sessions): int
    {
        $durations = [];

        foreach ($sessions as $session) {
            if (! empty($session->time_from) && ! empty($session->time_to)) {
                $from = $this->minutesOfDay((string) $session->time_from);
                $to   = $this->minutesOfDay((string) $session->time_to);
                if ($to > $from) {
                    $durations[] = $to - $from;
                }
            }
        }

        if ($durations === []) {
            return 120; // sane 2-hour default when nothing can be averaged
        }

        return (int) round(array_sum($durations) / count($durations));
    }

    private function minutesOfDay(string $time): int
    {
        [$h, $m] = array_pad(explode(':', $time), 2, '0');

        return ((int) $h) * 60 + (int) $m;
    }

    private function sessionHasEnded(object $session, \Illuminate\Support\Carbon $now, int $avgMinutes): bool
    {
        if ($session->session_date === null) {
            return false; // unscheduled → can't have ended yet
        }

        $end = \Illuminate\Support\Carbon::parse($session->session_date)->startOfDay();

        if (! empty($session->time_to)) {
            // Explicit end time wins.
            [$h, $m, $s] = array_pad(explode(':', (string) $session->time_to), 3, '0');
            $end->setTime((int) $h, (int) $m, (int) $s);
        } elseif (! empty($session->time_from)) {
            // No end → auto-end after the cohort's average length.
            [$h, $m, $s] = array_pad(explode(':', (string) $session->time_from), 3, '0');
            $end->setTime((int) $h, (int) $m, (int) $s)->addMinutes($avgMinutes);
        } else {
            // Whole-day session with no clock → ends when the day is over.
            $end->endOfDay();
        }

        return $end->lessThan($now);
    }

    private function activeCoursesQuery(User $user)
    {
        $today = now()->toDateString();

        return $this->course->newQuery()
            ->select(['courses.id', 'courses.title', 'courses.course_type', 'courses.image', 'courses.hours'])
            ->with([
                'category:id,name',
                'instructors:id,name,image',
                // Eager-load the enrolment pivot for *this* user so
                // we can resolve the cohort id without a second query.
                'usersCourses' => fn ($q) => $q->where('user_id', $user->id),
                'usersCourses.group' => fn ($q) => $q
                    ->select(['id', 'course_id', 'name', 'start_date', 'end_date', 'capacity', 'status']),
            ])
            ->whereExists(function ($q) use ($user, $today) {
                $q->from('users_courses')
                  ->whereColumn('users_courses.course_id', 'courses.id')
                  ->where('users_courses.user_id', $user->id);

                // Active cohort: end_date in the future OR null.
                $q->where(function ($q2) use ($today) {
                    $q2->whereNotIn('users_courses.group_id', function ($q3) use ($today) {
                        // Exclude cohorts whose end_date has passed.
                        $q3->select('id')
                           ->from('course_sections')
                           ->whereNotNull('end_date')
                           ->whereDate('end_date', '<', $today);
                    });
                });
            })
            ->orderByDesc('courses.id');
    }
}
