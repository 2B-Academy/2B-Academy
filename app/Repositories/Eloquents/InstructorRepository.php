<?php

namespace App\Repositories\Eloquents;

use App\Models\Instructor;
use App\Repositories\Contracts\InstructorRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class InstructorRepository extends BaseRepository implements InstructorRepositoryInterface
{
    public function __construct(Instructor $model)
    {
        parent::__construct($model);
    }

    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->when($search, fn ($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->withCount('courses')
            ->latest()
            ->paginate($perPage);
    }
}
