<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface ArticleRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(int $perPage, ?string $type, ?string $search): LengthAwarePaginator;
}
