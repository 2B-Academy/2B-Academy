<?php

namespace App\Repositories\Contracts;

interface EvaluationRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(int $perPage = 20, ?string $search = null, ?int $categoryId = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    public function allForCategory(int $categoryId): \Illuminate\Database\Eloquent\Collection;
}
