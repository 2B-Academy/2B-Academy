<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Enums\Mobile\EnrolmentOutcome;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Repositories\Contracts\Mobile\AcademyRepositoryInterface;
use App\Repositories\Contracts\Mobile\MobileEnrolmentRepositoryInterface;
use Illuminate\Support\Facades\DB;

/**
 * Implements the first-come-first-served enrolment race for S-03.
 *
 * Sequence:
 *   1. Pick an anchor cohort (caller-specified or next joinable).
 *   2. Open a DB transaction and lock the cohort row.
 *   3. Re-read seat count + deadline inside the lock so we don't race
 *      against a concurrent insert.
 *   4. Insert the users_courses row (idempotent on user+course).
 *
 * Returns a typed outcome that the controller maps to HTTP status +
 * translation key.
 */
final class EnrolmentService
{
    public function __construct(
        private readonly AcademyRepositoryInterface         $academyRepository,
        private readonly MobileEnrolmentRepositoryInterface $enrolmentRepository,
        private readonly AcademyService                     $academyService,
    ) {}

    /**
     * @return array{outcome: EnrolmentOutcome, cohort: ?CourseSection}
     */
    public function enrol(User $user, Course $course, ?int $requestedCohortId): array
    {
        return DB::transaction(function () use ($user, $course, $requestedCohortId): array {
            // 1. Already enrolled? Idempotent success — let the
            //    confirmation screen point the user to My Learning.
            if ($this->academyRepository->isEnrolledInCourse($user, $course->id)) {
                $existing = $this->resolveUsersExistingCohort($user, $course);
                return ['outcome' => EnrolmentOutcome::AlreadyEnrolled, 'cohort' => $existing];
            }

            // 2. Resolve cohort.
            $cohort = $requestedCohortId !== null
                ? CourseSection::query()->where('course_id', $course->id)->find($requestedCohortId)
                : $this->academyRepository->nextJoinableCohort(
                    $course,
                    $user,
                    now(),
                    0, // close-offset already applied via the academy default
                    $this->academyService->scheduledVisibilityDays(),
                );
            if ($cohort === null) {
                // Re-fetch via the academy service to keep deadline
                // logic in one place.
                $cohort = $this->academyService->anchorCohortFor($course, $user);
            }
            if ($cohort === null) {
                return ['outcome' => EnrolmentOutcome::NoCohort, 'cohort' => null];
            }

            // 3. Deadline still alive?
            $deadline = $this->academyService->effectiveDeadline($cohort);
            if ($deadline !== null && $deadline->isPast()) {
                return ['outcome' => EnrolmentOutcome::EnrolmentClosed, 'cohort' => $cohort];
            }

            // 4. Capacity check inside the row lock.
            $enrolled = $this->enrolmentRepository->lockAndCountSeats($cohort->id);
            if ($cohort->capacity !== null && $enrolled >= (int) $cohort->capacity) {
                return ['outcome' => EnrolmentOutcome::CohortFull, 'cohort' => $cohort];
            }

            // 5. Commit the enrolment.
            $this->enrolmentRepository->createEnrolment($user, $cohort);

            return ['outcome' => EnrolmentOutcome::Enrolled, 'cohort' => $cohort];
        });
    }

    private function resolveUsersExistingCohort(User $user, Course $course): ?CourseSection
    {
        $cohortId = DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->value('group_id');

        if (empty($cohortId)) {
            return null;
        }

        return CourseSection::query()->find($cohortId);
    }
}
