<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\CourseSession;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Admin-side passcode generation / rotation for a CourseSession.
 *
 * Length defaults to the platform `attendance_passcode_length` but
 * can be overridden per-call (the IssuePasscodeRequest exposes the
 * override). Expiry defaults to now() + `attendance_window_minutes`
 * but can be pinned via a caller-supplied timestamp.
 *
 * The generator favors numeric-only codes (matches the S-06 numeric
 * keypad) and uses random_int() so it's safe for the modest entropy
 * we need (5–6 digits over a short validity window).
 */
final class SessionPasscodeService
{
    public function __construct(private readonly MobileSettings $settings) {}

    public function issue(CourseSession $session, ?int $lengthOverride, ?Carbon $expiresAt): CourseSession
    {
        $length = $lengthOverride !== null && $lengthOverride > 0
            ? $lengthOverride
            : $this->settings->attendancePasscodeLength();

        $now = now();
        // The validity depends on the platform passcode mode:
        //   • static   → the code lives for the whole session window
        //   • rotating → the code resets every `passcode_reset_seconds`
        // A caller-supplied `expiresAt` always wins (admin override).
        $effectiveExpiry = $expiresAt ?? $this->defaultExpiryFor($session, $now);
        $validityMinutes = max(1, (int) ceil($now->diffInSeconds($effectiveExpiry, false) / 60));

        $session->forceFill([
            'passcode'                  => $this->randomNumericCode($length),
            'passcode_issued_at'        => $now,
            'passcode_expires_at'       => $effectiveExpiry,
            'attendance_window_minutes' => $expiresAt === null ? $validityMinutes : null,
        ])->save();

        return $session->fresh();
    }

    /**
     * Resolve when a freshly-issued passcode should expire when the caller
     * did not pin an explicit timestamp.
     *
     * Static mode keeps the code valid until the session's own window
     * closes (time_to + grace), so attendance stays open the whole class.
     * Rotating mode keeps it short-lived (`passcode_reset_seconds`) but
     * never lets it outlive the session window.
     */
    private function defaultExpiryFor(CourseSession $session, Carbon $now): Carbon
    {
        $grace      = $this->settings->attendanceSessionGraceMinutes();
        $windowEnd  = $this->sessionWindowEnd($session, $now);
        $windowEnd  = $windowEnd?->copy()->addMinutes($grace);

        if ($this->settings->passcodeStaticForSession()) {
            return $windowEnd ?? $now->copy()->addMinutes($this->settings->attendanceWindowMinutes());
        }

        $expiry = $now->copy()->addSeconds($this->settings->passcodeResetSeconds());

        // A rotating code must never claim to be valid after the session
        // window itself has closed.
        if ($windowEnd !== null && $expiry->gt($windowEnd)) {
            return $windowEnd;
        }

        return $expiry;
    }

    /**
     * The wall-clock end of a session's attendance window, derived from
     * `session_date` + `time_to`. Null when the session has no time bound
     * (date-only sessions are open all day).
     */
    private function sessionWindowEnd(CourseSession $session, Carbon $now): ?Carbon
    {
        $timeTo = $session->time_to;
        if ($timeTo === null || $timeTo === '') {
            return null;
        }

        $date = $session->session_date ? substr((string) $session->session_date, 0, 10) : $now->toDateString();

        try {
            return Carbon::parse($date . ' ' . $timeTo);
        } catch (\Throwable) {
            return null;
        }
    }

    public function revoke(CourseSession $session): CourseSession
    {
        $session->forceFill([
            'passcode'                  => null,
            'passcode_issued_at'        => null,
            'passcode_expires_at'       => null,
            'attendance_window_minutes' => null,
        ])->save();

        return $session->fresh();
    }

    private function randomNumericCode(int $length): string
    {
        $digits = '';
        for ($i = 0; $i < $length; $i++) {
            $digits .= (string) random_int(0, 9);
        }
        // Defensive: ensure the leading digit isn't zero so the
        // mobile client doesn't accidentally treat the code as an
        // integer and drop the leading zero in storage.
        if ($digits[0] === '0') {
            $digits[0] = (string) random_int(1, 9);
        }
        return $digits;
    }
}
