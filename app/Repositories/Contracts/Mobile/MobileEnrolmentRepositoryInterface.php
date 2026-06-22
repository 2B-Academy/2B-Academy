<?php

declare(strict_types=1);

namespace App\Repositories\Contracts\Mobile;

use App\Models\CourseSection;
use App\Models\User;

/**
 * Write surface for S-03 → S-04 (enrolment + idempotent insert).
 */
interface MobileEnrolmentRepositoryInterface
{
    /**
     * Re-count enrolments for the cohort under a transactional lock.
     * Used by EnrolmentService for the first-come-first-served check.
     */
    public function lockAndCountSeats(int $cohortId): int;

    /**
     * Insert the users_courses row, idempotent on `(course_id, user_id)`.
     */
    public function createEnrolment(User $user, CourseSection $cohort): void;
}
