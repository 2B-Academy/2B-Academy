<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

/**
 * Outcome of a single enrolment attempt.
 *
 * The mobile S-03 → S-04 flow expects an unambiguous, machine-readable
 * outcome so the client can route between the confirmation screen, the
 * "this cohort just filled up" snackbar, and the "Enrolled ✓ — view
 * in My Learning" CTA. The values double as the translation key
 * suffix used by EnrolmentService (`messages.mobile.enrolment_*`).
 */
enum EnrolmentOutcome: string
{
    case Enrolled         = 'enrolled';
    case AlreadyEnrolled  = 'already_enrolled';
    case CohortFull       = 'cohort_full';
    case EnrolmentClosed  = 'enrolment_closed';
    case NoCohort         = 'no_cohort';

    public function isSuccess(): bool
    {
        return $this === self::Enrolled || $this === self::AlreadyEnrolled;
    }

    public function httpStatus(): int
    {
        return match ($this) {
            self::Enrolled        => 201,
            self::AlreadyEnrolled => 200,
            self::CohortFull,
            self::EnrolmentClosed,
            self::NoCohort        => 409,
        };
    }

    public function messageKey(): string
    {
        return match ($this) {
            self::Enrolled        => 'messages.mobile.enrolment_success',
            self::AlreadyEnrolled => 'messages.mobile.enrolment_already',
            self::CohortFull      => 'messages.mobile.enrolment_cohort_full',
            self::EnrolmentClosed => 'messages.mobile.enrolment_closed',
            self::NoCohort        => 'messages.mobile.enrolment_no_cohort',
        };
    }
}
