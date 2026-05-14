<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface CourseRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(
        int     $perPage,
        ?string $search,
        ?int    $categoryId,
        ?bool   $active,
        ?string $courseType,
    ): LengthAwarePaginator;

    public function allActive(): Collection;

    public function findWithRelations(int $id): Course;
}
