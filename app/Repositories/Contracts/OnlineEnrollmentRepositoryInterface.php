<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface OnlineEnrollmentRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForCourse(int $courseId, int $perPage, ?string $search): LengthAwarePaginator;

    public function getEnrolledUserIds(int $courseId): array;

    public function syncUsers(int $courseId, array $userIds): void;

    public function attachUsers(int $courseId, array $userIds): void;

    public function detachUser(int $courseId, int $userId): int;
}
