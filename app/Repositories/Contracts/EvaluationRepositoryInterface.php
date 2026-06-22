<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use App\Models\Evaluation;

interface EvaluationRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithFilters(int $perPage = 20, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator;
    public function allForCategory(int $categoryId): Collection;
    public function findWithCategory(int $id): Evaluation;
}
