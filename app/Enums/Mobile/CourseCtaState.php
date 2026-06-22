<?php

declare(strict_types=1);

namespace App\Enums\Mobile;

/**
 * Drives the S-03 sticky CTA + the S-02 card chip.
 *
 *  - EnrolNow         : open seats, deadline not yet passed
 *  - GetNotified      : enrolment closed for the next cohort
 *  - EnrolledViewLearning : the learner already has a seat
 *  - Unavailable      : cohort full / no upcoming cohort
 *  - NotEnrollable    : course flagged as not enrollable (admin/public)
 */
enum CourseCtaState: string
{
    case EnrolNow             = 'enrol_now';
    case GetNotified          = 'get_notified';
    case EnrolledViewLearning = 'enrolled_view_learning';
    case Unavailable          = 'unavailable';
    case NotEnrollable        = 'not_enrollable';

    public function isInteractive(): bool
    {
        return $this === self::EnrolNow
            || $this === self::EnrolledViewLearning;
    }
}
