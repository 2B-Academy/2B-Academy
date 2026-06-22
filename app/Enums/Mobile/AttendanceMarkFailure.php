<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

/**
 * Why a passcode submission could not be accepted.
 *
 * S-06 surfaces specific, actionable copy for each failure mode
 * (Nielsen #9 — help recover from errors). The enum mirrors the
 * translation keys under `messages.mobile.attendance_*`.
 */
enum AttendanceMarkFailure: string
{
    case InvalidCode    = 'invalid_code';
    case ExpiredCode    = 'expired_code';
    case NoOpenWindow   = 'no_open_window';
    case AlreadyMarked  = 'already_marked';
    case NotEnrolled    = 'not_enrolled';

    public function httpStatus(): int
    {
        return match ($this) {
            // A wrong passcode is an expected, recoverable outcome — the
            // mobile client re-prompts the learner. We return HTTP 200 so
            // it isn't treated as a hard error; the body still carries
            // `success: false` and the same explanatory message.
            self::InvalidCode    => 200,
            self::ExpiredCode    => 422,
            self::NoOpenWindow   => 409,
            self::AlreadyMarked  => 409,
            self::NotEnrolled    => 403,
        };
    }

    public function messageKey(): string
    {
        return match ($this) {
            self::InvalidCode    => 'messages.mobile.attendance_invalid_code',
            self::ExpiredCode    => 'messages.mobile.attendance_expired_code',
            self::NoOpenWindow   => 'messages.mobile.attendance_no_open_window',
            self::AlreadyMarked  => 'messages.mobile.attendance_already_marked',
            self::NotEnrolled    => 'messages.course_not_enrolled',
        };
    }
}
