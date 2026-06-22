<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Contracts\OnlineEnrollmentRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class OnlineEnrollmentService
{
    public function __construct(
        private readonly OnlineEnrollmentRepositoryInterface $enrollmentRepository,
    ) {}

    public function paginate(int $courseId, int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->enrollmentRepository->paginateForCourse($courseId, $perPage, $search);
    }

    /** Add users to course without removing existing ones. */
    public function attach(Course $course, array $userIds): void
    {
        $this->enrollmentRepository->attachUsers($course->id, $userIds);
    }

    /** Replace all enrolled users for the course. */
    public function sync(Course $course, array $userIds): void
    {
        $this->enrollmentRepository->syncUsers($course->id, $userIds);
    }

    /** Toggle the for_public flag on the course. */
    public function setPublic(Course $course, bool $forPublic): void
    {
        $course->update(['for_public' => $forPublic]);
    }

    public function detach(Course $course, int $userId): void
    {
        $this->enrollmentRepository->detachUser($course->id, $userId);
    }

    public function getEnrolledUserIds(int $courseId): array
    {
        return $this->enrollmentRepository->getEnrolledUserIds($courseId);
    }
}
