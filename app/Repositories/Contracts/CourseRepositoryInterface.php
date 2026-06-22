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
        ?string $status = null,
    ): LengthAwarePaginator;

    /**
     * @return array{all: int, active: int, inactive: int, pending: int, upcoming: int}
     */
    public function tabCounts(): array;

    public function allActive(): Collection;

    public function findWithRelations(int $id): Course;

    public function findWithBasicRelations(int $id): Course;

    public function activePluckedTitles(): Collection;
}
