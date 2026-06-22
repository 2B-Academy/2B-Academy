<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Services\Mobile\MobileSettings;
use App\Services\Mobile\SessionPasscodeService;
use Illuminate\Support\Carbon;

/**
 * Composes the instructor-dashboard "Passcode" widget view-model
 * (Figma node 515:34995 / 515:37969 / 515:35489).
 *
 * The dashboard never needs a session id up-front — it resolves the
 * "current" session itself:
 *
 *   • a session that already carries an ACTIVE passcode  → state "live"
 *   • a session whose passcode was issued today but has
 *     since expired                                       → state "ended"
 *   • a session that is live-now by schedule (no code yet) → state "idle"
 *   • nothing relevant today                               → state "idle"
 *
 * "Generate Passcode" then targets the live-now session (or the
 * earliest session scheduled for today as a fallback) and delegates
 * the actual code generation to the existing SessionPasscodeService so
 * the admin dashboard and the mobile S-06 flow stay in lock-step.
 *
 * This is an ADDITIVE service — it never touches MVC and reuses the
 * same attendance-window buffers as MyLearningService::liveSessionFor()
 * so "Live Now" on mobile and "Generate Passcode" here agree.
 */
final class DashboardPasscodeService
{
    public function __construct(
        private readonly MobileSettings $settings,
        private readonly SessionPasscodeService $passcodes,
    ) {}

    /**
     * Read-only widget state for the dashboard header, scoped to the
     * sessions the logged-in instructor actually teaches.
     *
     * @param  array<int, int>|null  $courseIds  Courses the instructor
     *         teaches. `null` = the viewer is not an instructor (or
     *         teaches nothing) → the widget is unavailable and hidden.
     * @return array<string, mixed>
     */
    public function currentState(?array $courseIds): array
    {
        $length = $this->settings->attendancePasscodeLength();

        // Not an instructor (or teaches nothing) → widget hidden.
        if (empty($courseIds)) {
            return [
                'available'       => false,
                'state'           => 'idle',
                'passcode_length' => $length,
                'rotates'         => ! $this->settings->passcodeStaticForSession(),
                'reset_seconds'   => $this->settings->passcodeResetSeconds(),
                'session'         => null,
                'passcode'        => null,
            ];
        }

        $now = now();

        // 1. A session the instructor already started today (it carries a
        //    passcode) whose attendance window is still open → "live".
        //
        //    There is NO "session ended" state: a started session stays
        //    live for its whole window. If the rotating code has lapsed we
        //    silently re-issue a fresh one here so a valid code always
        //    shows with zero user action (the dashboard then keeps it
        //    rotating). The only way to stop it is the "End Session" button.
        $startedLive = $this->startedLiveSession($now, $courseIds);

        if ($startedLive) {
            $expiresAt = $startedLive->passcode_expires_at !== null
                ? Carbon::parse((string) $startedLive->passcode_expires_at)
                : null;

            // Re-issue when the code has lapsed, or — in rotating mode — when
            // its remaining validity is far longer than the reset interval
            // (i.e. it was issued under a different policy, e.g. a static
            // session before the platform was switched to rotating). This
            // self-heals the live session so it actually rotates without the
            // instructor having to restart it.
            $needsReissue = $expiresAt === null || $expiresAt->lte($now);

            if (! $needsReissue && ! $this->settings->passcodeStaticForSession()) {
                $rotationCeiling = $now->copy()->addSeconds($this->settings->passcodeResetSeconds() + 5);
                $needsReissue    = $expiresAt->gt($rotationCeiling);
            }

            return $needsReissue
                ? $this->issueFor($startedLive, null, null)
                : $this->compose($startedLive, 'live', $length);
        }

        // 2. A session that is live-now by schedule but was never started
        //    (no passcode yet) → "idle" (the "Generate Passcode" affordance).
        $live = $this->liveNowSession($now, $courseIds);
        if ($live) {
            return $this->compose($live, 'idle', $length);
        }

        // 3. Instructor with no live session right now — widget is shown
        //    but the "Generate" button is disabled client-side.
        return [
            'available'       => true,
            'state'           => 'idle',
            'passcode_length' => $length,
            'rotates'         => ! $this->settings->passcodeStaticForSession(),
            'reset_seconds'   => $this->settings->passcodeResetSeconds(),
            'session'         => null,
            'passcode'        => null,
        ];
    }

    /**
     * Courses the instructor can start a session for, each with its
     * still-runnable cohorts. Feeds the dashboard course/cohort pickers.
     *
     * A course is excluded only when ALL of its cohorts have ended
     * (every `end_date < today`). Cohorts with no `end_date` are treated
     * as open-ended and always selectable.
     *
     * @param  array<int, int>|null  $courseIds  Courses the instructor teaches.
     * @return array<int, array<string, mixed>>
     */
    public function eligibleCourses(?array $courseIds): array
    {
        if (empty($courseIds)) {
            return [];
        }

        $today  = now()->toDateString();
        $locale = app()->getLocale();

        return Course::query()
            ->whereIn('id', $courseIds)
            ->with(['sections' => function ($q) use ($today) {
                // Only cohorts that have not ended yet are selectable.
                $q->where(function ($q2) use ($today) {
                    $q2->whereNull('end_date')
                       ->orWhereDate('end_date', '>=', $today);
                })
                ->orderBy('start_date')
                ->orderBy('id');
            }])
            ->orderBy('id')
            ->get()
            // "all_ended" exclusion: drop courses left with no runnable cohort.
            ->filter(fn (Course $c) => $c->sections->isNotEmpty())
            ->map(fn (Course $c) => [
                'id'      => (int) $c->id,
                'title'   => $c->getTranslation('title', $locale),
                'cohorts' => $c->sections->map(fn (CourseSection $s) => [
                    'id'         => (int) $s->id,
                    'name'       => $s->getTranslation('name', $locale),
                    'start_date' => $s->start_date ? $s->start_date->format('Y-m-d') : null,
                    'end_date'   => $s->end_date ? $s->end_date->format('Y-m-d') : null,
                ])->values()->all(),
            ])
            ->values()
            ->all();
    }

    /**
     * Validate that a cohort belongs to the given course and is still
     * runnable (not ended). Returns the cohort, or null when it is not a
     * legitimate target for a new session.
     */
    public function findEligibleCohort(int $courseId, int $cohortId): ?CourseSection
    {
        $today = now()->toDateString();

        return CourseSection::query()
            ->where('id', $cohortId)
            ->where('course_id', $courseId)
            ->where(function ($q) use ($today) {
                $q->whereNull('end_date')
                  ->orWhereDate('end_date', '>=', $today);
            })
            ->first();
    }

    /**
     * Start a brand-new session for the chosen course + cohort (today,
     * beginning now for the configured attendance window) and issue its
     * passcode in the same call. Returns the composed "live" widget state.
     *
     * @return array<string, mixed>
     */
    public function startSessionAndIssue(int $courseId, int $cohortId, ?int $length, ?Carbon $expiresAt): array
    {
        $now = now();

        // Session length is driven by the cohort's "Avg. Session Time"
        // (stored in hours) when set, otherwise the global attendance
        // window. This lets each cohort run sessions of a different
        // length without touching the platform default (Figma 332:9988).
        $cohort   = CourseSection::find($cohortId);
        $avgHours = $cohort?->avg_session_time !== null ? (float) $cohort->avg_session_time : null;
        $windowMinutes = $avgHours !== null && $avgHours > 0
            ? (int) round($avgHours * 60)
            : $this->settings->attendanceWindowMinutes();
        $windowMinutes = max(1, $windowMinutes);
        $end           = $now->copy()->addMinutes($windowMinutes);

        // Keep the session within the same calendar day so the time-only
        // window math (TIMESTAMPDIFF on `time_from`/`time_to`) stays sane.
        $timeTo = $end->isSameDay($now) ? $end->format('H:i:s') : '23:59:59';

        $session = CourseSession::create([
            'course_id'    => $courseId,
            'section_id'   => $cohortId,
            'title'        => __('messages.passcode.session_title', ['date' => $now->format('d M Y')]),
            'session_date' => $now->toDateString(),
            'time_from'    => $now->format('H:i:s'),
            'time_to'      => $timeTo,
        ]);

        $session->load(['section', 'course']);

        return $this->issueFor($session, $length, $expiresAt);
    }

    /**
     * Rotate the passcode on the instructor's currently-live session and
     * return the refreshed "live" widget state. Powers the dashboard
     * "Regenerate" button and the rotating-passcode auto-refresh.
     *
     * Only a session whose attendance window is still open is a valid
     * target — re-issuing a code on a finished session would be useless
     * (the mobile S-06 lookup also enforces the time window). When nothing
     * is live we just return the read-only current state (ended / idle),
     * which lets the dashboard fall back to the "Start a Session" picker.
     *
     * @param  array<int, int>|null  $courseIds
     * @return array<string, mixed>
     */
    public function regenerateCurrent(?array $courseIds): array
    {
        if (empty($courseIds)) {
            return $this->currentState($courseIds);
        }

        $now = now();
        // Prefer the session the instructor actually started; only fall
        // back to a bare scheduled live-now session if needed.
        $session = $this->startedLiveSession($now, $courseIds)
            ?? $this->liveNowSession($now, $courseIds);

        if ($session === null) {
            // Nothing live to rotate — surface the plain current state so
            // the widget reflects reality (and offers a fresh session).
            return $this->currentState($courseIds);
        }

        return $this->issueFor($session, null, null);
    }

    /**
     * End the instructor's current live session: revoke its passcode and
     * close the attendance window now, so it stops being "live" and the
     * rotating-passcode auto-refresh halts. Returns the refreshed (idle)
     * widget state. Powers the dashboard "End Session" button.
     *
     * @param  array<int, int>|null  $courseIds
     * @return array<string, mixed>
     */
    public function endCurrent(?array $courseIds): array
    {
        if (empty($courseIds)) {
            return $this->currentState($courseIds);
        }

        $now     = now();
        $session = $this->startedLiveSession($now, $courseIds)
            ?? $this->liveNowSession($now, $courseIds);

        // Fall back to a session that still carries a code today even if
        // its scheduled window has already drifted shut.
        if ($session === null) {
            $session = CourseSession::query()
                ->with(['section', 'course'])
                ->whereIn('course_id', $courseIds)
                ->whereNotNull('passcode')
                ->whereDate('passcode_issued_at', $now->toDateString())
                ->orderByDesc('passcode_issued_at')
                ->first();
        }

        if ($session !== null) {
            // Push the window end safely past the grace buffer so the
            // session is immediately excluded from every "live" lookup,
            // then drop the passcode entirely.
            $closedAt = $now->copy()->subMinutes($this->settings->attendanceSessionGraceMinutes() + 1);
            $timeTo   = $closedAt->isSameDay($now) ? $closedAt->format('H:i:s') : '00:00:01';

            $session->forceFill([
                'time_to'                   => $timeTo,
                'passcode'                  => null,
                'passcode_issued_at'        => null,
                'passcode_expires_at'       => null,
                'attendance_window_minutes' => null,
            ])->save();
        }

        return $this->currentState($courseIds);
    }

    /**
     * Resolve the session a freshly-pressed "Generate Passcode" should
     * target: the instructor's live-now session, falling back to the
     * earliest session scheduled for today. Null = nothing to generate.
     *
     * @param  array<int, int>  $courseIds
     */
    public function resolveTargetSession(array $courseIds): ?CourseSession
    {
        if (empty($courseIds)) {
            return null;
        }

        $now = now();

        return $this->liveNowSession($now, $courseIds)
            ?? $this->earliestTodaySession($now, $courseIds);
    }

    /**
     * Issue (or rotate) a passcode for the resolved session and return
     * the freshly-composed "live" widget state for the modal.
     *
     * @return array<string, mixed>
     */
    public function issueFor(CourseSession $session, ?int $length, ?Carbon $expiresAt): array
    {
        $fresh = $this->passcodes->issue($session, $length, $expiresAt);

        // Re-attach the relations the freshly-pulled model lost so
        // compose() doesn't fire two extra lazy queries.
        $fresh->setRelation('section', $session->section);
        $fresh->setRelation('course', $session->course);

        return $this->compose($fresh, 'live', $this->settings->attendancePasscodeLength());
    }

    // ────────────────────────────────────────────────────────────
    // Internals
    // ────────────────────────────────────────────────────────────

    /**
     * Session whose attendance window is open right now. Mirrors the
     * predicate in MyLearningService::liveSessionFor() but is not
     * scoped to a single learner/course (the dashboard is global).
     */
    /**
     * The session the instructor already started today — it carries a
     * passcode and its attendance window is still open. This is what makes
     * a session "live" regardless of whether the current rotating code
     * instance has lapsed (the dashboard re-issues a fresh one). Returns
     * the most recently-started such session.
     */
    private function startedLiveSession(Carbon $now, array $courseIds): ?CourseSession
    {
        $openBuf  = $this->settings->attendanceSessionOpenBufferMinutes();
        $graceBuf = $this->settings->attendanceSessionGraceMinutes();

        return CourseSession::query()
            ->with(['section', 'course'])
            ->whereIn('course_id', $courseIds)
            ->whereNotNull('passcode')
            ->whereDate('passcode_issued_at', $now->toDateString())
            ->where(function ($q) use ($now) {
                $q->whereNull('session_date')
                    ->orWhereDate('session_date', $now->toDateString());
            })
            ->where(function ($q) use ($now, $openBuf, $graceBuf) {
                $q->where(function ($q2) {
                    $q2->whereNull('time_from')->whereNull('time_to');
                })->orWhere(function ($q2) use ($now, $openBuf, $graceBuf) {
                    $q2->whereRaw(
                        'time_from <= ADDTIME(?, SEC_TO_TIME(? * 60))',
                        [$now->format('H:i:s'), $openBuf],
                    )->whereRaw(
                        'time_to >= SUBTIME(?, SEC_TO_TIME(? * 60))',
                        [$now->format('H:i:s'), $graceBuf],
                    );
                });
            })
            ->orderByDesc('passcode_issued_at')
            ->first();
    }

    private function liveNowSession(Carbon $now, array $courseIds): ?CourseSession
    {
        $openBuf  = $this->settings->attendanceSessionOpenBufferMinutes();
        $graceBuf = $this->settings->attendanceSessionGraceMinutes();

        return CourseSession::query()
            ->with(['section', 'course'])
            ->whereIn('course_id', $courseIds)
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
                    // directly (mirrors MyLearningService::liveSessionFor()).
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

    private function earliestTodaySession(Carbon $now, array $courseIds): ?CourseSession
    {
        return CourseSession::query()
            ->with(['section', 'course'])
            ->whereIn('course_id', $courseIds)
            ->whereDate('session_date', $now->toDateString())
            ->orderBy('time_from')
            ->orderBy('id')
            ->first();
    }

    /**
     * 1-based position of the session inside its cohort, ordered the
     * way a human reads a syllabus (date → start time → id). Drives the
     * "Session: 3" line in the Live Passcode modal.
     */
    private function sessionOrdinal(CourseSession $session): int
    {
        if ($session->section_id === null) {
            return 1;
        }

        $ids = CourseSession::query()
            ->where('section_id', $session->section_id)
            ->orderByRaw('session_date IS NULL')
            ->orderBy('session_date')
            ->orderBy('time_from')
            ->orderBy('id')
            ->pluck('id');

        $index = $ids->search($session->id);

        return $index === false ? 1 : ((int) $index + 1);
    }

    /**
     * @return array<string, mixed>
     */
    private function compose(CourseSession $session, string $state, int $length): array
    {
        $locale  = app()->getLocale();
        $expires = $session->passcode_expires_at
            ? Carbon::parse((string) $session->passcode_expires_at)
            : null;

        $passcode = $session->passcode
            ? [
                'code'       => (string) $session->passcode,
                'issued_at'  => $session->passcode_issued_at
                    ? Carbon::parse((string) $session->passcode_issued_at)->toIso8601String()
                    : null,
                'expires_at' => $expires?->toIso8601String(),
                'expired'    => $expires ? $expires->lte(now()) : false,
            ]
            : null;

        return [
            'available'       => true,
            'state'           => $state,
            'passcode_length' => $length,
            // Tell the dashboard whether the code rotates so it can show a
            // live countdown + auto re-issue a fresh code, or keep it static
            // for the whole session.
            'rotates'         => ! $this->settings->passcodeStaticForSession(),
            'reset_seconds'   => $this->settings->passcodeResetSeconds(),
            'session'         => [
                'id'           => (int) $session->id,
                'number'       => $this->sessionOrdinal($session),
                'title'        => $session->title,
                'date'         => $session->session_date
                    ? Carbon::parse((string) $session->session_date)->toDateString()
                    : null,
                'time_from'    => $this->formatTime($session->time_from),
                'time_to'      => $this->formatTime($session->time_to),
                'course_id'    => (int) $session->course_id,
                'course_title' => $session->course?->getTranslation('title', $locale),
                'cohort_id'    => $session->section_id !== null ? (int) $session->section_id : null,
                'cohort_name'  => $session->section?->getTranslation('name', $locale),
            ],
            'passcode'        => $passcode,
        ];
    }

    private function formatTime(?string $time): ?string
    {
        if ($time === null || $time === '') {
            return null;
        }

        return substr($time, 0, 5);
    }
}
