<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSection;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Cohort Attendance rollup — drives the "Attendance Record" drawer on the
 * course detail screen.
 *
 * Design notes
 *   • The drawer needs BOTH a sessions view (rows per session, with the
 *     names of who was absent) and a learners view (rows per enrolled
 *     learner, with which sessions they missed) plus three filter chips
 *     (All / Presence / Absence). Doing this on the client over multiple
 *     endpoints would mean N session calls + N learner calls; instead we
 *     compute the whole matrix server-side in O(sessions + learners +
 *     attendances) and ship it in one response.
 *
 *   • Each attendance row is matched to a session either by its
 *     `session_id` column (preferred — added by the additive migration
 *     2026_05_24_120000) or, for legacy rows, by date equality between
 *     `attendances.created_at::date` and `course_sessions.session_date`.
 *     Sessions on the same day with NULL `session_id` will all credit
 *     the learner — that's the documented legacy behaviour.
 *
 *   • Learners come from `users_courses` (offline enrollment), filtered
 *     to the section so we never count a learner from another cohort.
 *     This is the same source the Cohorts tab uses, keeping the drawer
 *     and the table totals consistent.
 */
class CohortAttendanceService
{
    public function __construct(
        private readonly AttendanceRepositoryInterface $attendanceRepo,
    ) {}

    /**
     * Build the full rollup payload.
     *
     * @return array{
     *     cohort: array{id:int, name:string, start_date:?string, end_date:?string},
     *     course: array{id:int, title:string},
     *     totals: array{sessions:int, learners:int, attended:int, absent:int},
     *     sessions: array<int, array<string,mixed>>,
     *     learners: array<int, array<string,mixed>>
     * }
     */
    public function build(Course $course, CourseSection $section): array
    {
        // 1. Sessions for this cohort, ordered by date then start time so the
        //    UI's "Session 1, Session 2, ..." indices are deterministic.
        $sessions = $section->sessions()
            ->where('course_id', $course->id)
            ->orderByRaw('COALESCE(session_date, created_at) asc')
            ->orderByRaw('COALESCE(time_from, "00:00:00") asc')
            ->get(['id', 'title', 'session_date', 'time_from', 'time_to', 'location']);

        // 2. Enrolled learners for this cohort (offline enrollment table).
        $learners = DB::table('users_courses as uc')
            ->join('users as u', 'u.id', '=', 'uc.user_id')
            ->where('uc.course_id', $course->id)
            ->where('uc.group_id',  $section->id)
            ->orderBy('u.name')
            ->get(['u.id', 'u.name', 'u.machine_code', 'u.department_name']);

        // 3. All attendance rows for this cohort, in one shot.
        $rows = $this->attendanceRepo->cohortRows($course->id, $section->id);

        // 4. Index sessions for fast date lookup (legacy rows have no session_id).
        $sessionsByDate = $sessions->groupBy(fn ($s) => (string) $s->session_date);
        $sessionIndex   = $sessions->pluck('id')->flip(); // id => position
        $totalLearners  = $learners->count();

        // 5. Build attendance bitmap: learner_id => session_id => true.
        //    A learner is "present" for a session if they have AT LEAST ONE
        //    attendance row that resolves to it. Multiple punches on the
        //    same session are deduped here.
        $bitmap = [];
        foreach ($rows as $row) {
            $sid = $row->session_id;
            if (!$sid && isset($sessionsByDate[$row->attended_on])) {
                // Legacy fall-through: if the attendance has no explicit
                // session_id, credit every session held on that date.
                foreach ($sessionsByDate[$row->attended_on] as $s) {
                    $bitmap[$row->user_id][$s->id] = true;
                }
                continue;
            }
            if ($sid && $sessionIndex->has($sid)) {
                $bitmap[$row->user_id][$sid] = true;
            }
        }

        // 6. Per-session aggregates (counts + names of absentees).
        $sessionsOut = $sessions->values()->map(function ($s, $i) use ($learners, $bitmap) {
            $absentNames = [];
            $attended    = 0;
            foreach ($learners as $l) {
                if (!empty($bitmap[$l->id][$s->id])) {
                    $attended++;
                } else {
                    $absentNames[] = ['id' => (int) $l->id, 'name' => (string) ($l->name ?? '')];
                }
            }
            $total = count($learners);

            return [
                'id'              => (int) $s->id,
                'index'           => $i + 1,
                'title'           => (string) $s->title,
                'date'            => $s->session_date,
                'time_from'       => $s->time_from,
                'time_to'         => $s->time_to,
                'location'        => $s->location,
                'attended_count'  => $attended,
                'absent_count'    => $total - $attended,
                'total'           => $total,
                'full_attendance' => $total > 0 && $attended === $total,
                'absent_learners' => $absentNames,
            ];
        })->all();

        // 7. Per-learner aggregates (counts + which sessions they missed).
        $learnersOut = $learners->values()->map(function ($l) use ($sessions, $bitmap) {
            $attended       = 0;
            $absentSessions = [];
            foreach ($sessions as $i => $s) {
                if (!empty($bitmap[$l->id][$s->id])) {
                    $attended++;
                } else {
                    $absentSessions[] = [
                        'id'    => (int) $s->id,
                        'index' => $i + 1,
                        'title' => (string) $s->title,
                        'date'  => $s->session_date,
                    ];
                }
            }
            $total = $sessions->count();

            return [
                'id'              => (int) $l->id,
                'name'            => (string) ($l->name ?? ''),
                'machine_code'    => $l->machine_code,
                'department'      => $l->department_name,
                'total_sessions'  => $total,
                'attended_count'  => $attended,
                'absent_count'    => $total - $attended,
                'absent_sessions' => $absentSessions,
            ];
        })->all();

        // 8. Cross-cutting totals for the header chips.
        $attendedTotal = array_sum(array_column($sessionsOut, 'attended_count'));
        $absentTotal   = array_sum(array_column($sessionsOut, 'absent_count'));

        return [
            'cohort' => [
                'id'   => (int) $section->id,
                'name' => $section->getTranslation('name', app()->getLocale()),
                // start_date / end_date are exposed for the drawer header
                // but `course_sections` doesn't store them yet — they fall
                // back to NULL until a follow-up migration adds them. The
                // drawer treats NULL as "no range to show".
                'start_date' => $section->start_date ?? null,
                'end_date'   => $section->end_date   ?? null,
            ],
            'course' => [
                'id'    => (int) $course->id,
                'title' => $course->getTranslation('title', app()->getLocale()),
            ],
            'totals' => [
                'sessions' => $sessions->count(),
                'learners' => $totalLearners,
                'attended' => (int) $attendedTotal,
                'absent'   => (int) $absentTotal,
            ],
            'sessions' => $sessionsOut,
            'learners' => $learnersOut,
        ];
    }
}
