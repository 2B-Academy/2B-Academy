<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\UsersCourse;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserEnrollmentRepositoryInterface
{
    public function paginateForCourse(Course $course, int $perPage, ?int $groupId): LengthAwarePaginator;
    public function syncUsers(Course $course, array $userIds, ?int $groupId): void;
    public function delete(UsersCourse $enrollment): void;
}
