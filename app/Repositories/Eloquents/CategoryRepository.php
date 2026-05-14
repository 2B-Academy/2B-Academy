<?php

namespace App\Repositories\Eloquents;

use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryRepository extends BaseRepository implements CategoryRepositoryInterface
{
    public function __construct(Category $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(int $perPage, ?string $search, ?bool $active): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->when($search, fn ($q) => $q->where('name', 'LIKE', "%{$search}%"))
            ->when(!is_null($active), fn ($q) => $q->where('active', $active))
            ->withCount('courses')
            ->latest()
            ->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()
            ->active()
            ->withCount('courses')
            ->latest()
            ->get();
    }
}
