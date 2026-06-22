<?php

declare(strict_types=1);

namespace App\Repositories\Contracts\Mobile;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection as SupportCollection;

/**
 * Read surface for the My Learning screen (S-05).
 *
 * Every row exposes live, derived data:
 *   - progress percentage     ← user_lecture_progress / course_lectures
 *   - sessions attended/total ← attendances vs course_sessions in the cohort
 *   - absence count           ← (past sessions) - (attendances for those sessions)
 *   - next unit               ← first non-completed lecture in cohort order
 *   - live now flag           ← any session today within
 *                                [time_from - openBuffer, time_to + graceBuffer]
 */
interface MyLearningRepositoryInterface
{
    /**
     * Active courses (enrolled and not yet completed) for the user.
     *
     * @return LengthAwarePaginator<Course>
     */
    public function activeCoursesForUser(User $user, int $perPage): LengthAwarePaginator;

    /**
     * Same shape, but already sliced for the overview preview.
     *
     * @return EloquentCollection<int, Course>
     */
    public function previewActiveCourses(User $user, int $limit): EloquentCollection;

    /**
     * Per-course aggregate stats for the My Learning cards.
     *
     * @return array{
     *     attended: int,
     *     past_sessions: int,
     *     total_sessions: int,
     *     absences: int,
     *     progress_percent: int,
     *     completed_lectures: int,
     *     total_lectures: int,
     *     next_unit_title: ?string,
     * }
     */
    public function courseProgressSummary(User $user, int $courseId, int $cohortId, string $locale): array;

    /**
     * Per-session attendance status for the "View Attendance" sheet.
     *
     * Returns a plain Support\Collection of stdClass rows (not Eloquent
     * models) — we go through the query builder for performance, so
     * the items aren't hydrated as full models.
     *
     * @return SupportCollection<int, object{
     *     id: int,
     *     title: string,
     *     session_date: ?string,
     *     time_from: ?string,
     *     time_to: ?string,
     *     attended: bool,
     * }>
     */
    public function sessionsAttendance(User $user, int $courseId, int $cohortId): SupportCollection;

    /**
     * The user's own rating row for a course, or `null`.
     */
    public function userRatingForCourse(User $user, int $courseId): ?object;

    /**
     * The session the learner is currently heading toward, expressed as
     * (number of finished sessions) + 1 — so it always starts at
     * "Session 1", even when the cohort has no sessions created yet.
     *
     * A session counts as finished once its explicit `time_to` has
     * passed, or — when it has no end time — once the cohort's average
     * session length has elapsed since its start (auto-end).
     *
     * Returns the 1-based sequence number plus a localized label
     * ("Session 3" / "الجلسة 3"). Always non-null for an enrolled cohort.
     *
     * @return array{number: int, name: string}|null
     */
    public function nextSessionFor(User $user, int $courseId, int $cohortId, string $locale): ?array;
}
