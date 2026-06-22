<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface QualificationSkillRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(int $perPage, ?string $search): LengthAwarePaginator;

    public function allForSelect(): Collection;
}
