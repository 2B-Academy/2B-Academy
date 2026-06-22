<?php

namespace App\Repositories\Eloquents;

use App\Models\CourseRating;
use App\Repositories\Contracts\CourseRatingRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class CourseRatingRepository extends BaseRepository implements CourseRatingRepositoryInterface
{
    public function __construct(CourseRating $model)
    {
        parent::__construct($model);
    }

    public function paginateForCourse(int $courseId, int $perPage, ?int $userId): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with(['user:id,name,machine_code'])
            ->where('course_id', $courseId)
            ->when($userId, fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->paginate($perPage);
    }

    public function upsertForUser(int $courseId, int $userId, array $data): CourseRating
    {
        /** @var CourseRating */
        return $this->model->newQuery()->updateOrCreate(
            ['course_id' => $courseId, 'user_id' => $userId],
            $data,
        );
    }

    public function paginateAll(int $perPage, ?int $courseId, ?int $instructorId, ?string $search): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with([
                'user:id,name',
                'course:id,title',
                'course.instructors:id,name',
            ])
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($instructorId, fn ($q) => $q->whereHas(
                'course.instructors',
                fn ($q2) => $q2->where('instructors.id', $instructorId),
            ))
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->whereHas('user', fn ($q3) => $q3->where('name', 'LIKE', "%{$search}%"))
                   ->orWhere('review', 'LIKE', "%{$search}%");
            }))
            ->latest()
            ->paginate($perPage);
    }
}
