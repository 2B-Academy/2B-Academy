<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

use App\Models\CourseRating;

interface CourseRatingRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForCourse(int $courseId, int $perPage, ?int $userId): LengthAwarePaginator;

    public function upsertForUser(int $courseId, int $userId, array $data): CourseRating;

    public function paginateAll(int $perPage, ?int $courseId, ?int $instructorId, ?string $search): LengthAwarePaginator;
}
