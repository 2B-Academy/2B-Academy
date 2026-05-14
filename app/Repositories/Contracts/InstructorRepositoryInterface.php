<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface InstructorRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator;
}
