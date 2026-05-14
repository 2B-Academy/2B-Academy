<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CourseRepository extends BaseRepository implements CourseRepositoryInterface
{
    public function __construct(Course $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(
        int     $perPage,
        ?string $search,
        ?int    $categoryId,
        ?bool   $active,
        ?string $courseType,
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            ->with(['category:id,name', 'instructors:id,name'])
            ->when($search, fn ($q) => $q->where('title', 'LIKE', "%{$search}%"))
            ->when($categoryId, fn ($q) => $q->where('category_id', $categoryId))
            ->when(!is_null($active), fn ($q) => $q->where('active', $active))
            ->when($courseType, fn ($q) => $q->where('course_type', $courseType))
            ->latest()
            ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->with('category:id,name')
            ->latest()
            ->get();
    }

    public function findWithRelations(int $id): Course
    {
        return $this->model->newQuery()
            ->with([
                'category:id,name',
                'instructors:id,name,image',
                'sections',
                'exams:id,course_id,title,degree,is_final',
            ])
            ->findOrFail($id);
    }
}
