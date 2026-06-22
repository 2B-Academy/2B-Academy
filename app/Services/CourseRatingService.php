<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseRating;
use App\Repositories\Contracts\CourseRatingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRatingService
{
    public function __construct(
        private readonly CourseRatingRepositoryInterface $ratingRepository,
    ) {}

    public function paginateForCourse(int $courseId, int $perPage = 20, ?int $userId = null): LengthAwarePaginator
    {
        return $this->ratingRepository->paginateForCourse($courseId, $perPage, $userId);
    }

    public function submitRating(Course $course, int $userId, array $data): CourseRating
    {
        return $this->ratingRepository->upsertForUser($course->id, $userId, $data);
    }

    public function delete(CourseRating $rating): bool
    {
        return $this->ratingRepository->delete($rating);
    }

    public function paginateAll(int $perPage = 20, ?int $courseId = null, ?int $instructorId = null, ?string $search = null): \Illuminate\Pagination\LengthAwarePaginator
    {
        return $this->ratingRepository->paginateAll($perPage, $courseId, $instructorId, $search);
    }
}
