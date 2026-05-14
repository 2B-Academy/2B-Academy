<?php

namespace App\Repositories\Contracts;

interface EvaluationCategoryRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(int $perPage = 20, ?string $search = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator;
    public function all(): \Illuminate\Database\Eloquent\Collection;
}
