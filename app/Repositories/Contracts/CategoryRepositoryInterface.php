<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(int $perPage, ?string $search, ?bool $active): LengthAwarePaginator;

    public function allActive(): \Illuminate\Support\Collection;
}
