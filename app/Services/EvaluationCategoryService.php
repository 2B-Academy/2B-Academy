<?php

namespace App\Services;

use App\Models\EvaluationCategory;
use App\Repositories\Contracts\EvaluationCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EvaluationCategoryService
{
    public function __construct(
        private readonly EvaluationCategoryRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginateWithSearch($perPage, $search);
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function find(int $id): EvaluationCategory
    {
        return $this->repo->findOrFail($id);
    }

    public function create(array $data): EvaluationCategory
    {
        return $this->repo->create($data);
    }

    public function update(EvaluationCategory $category, array $data): EvaluationCategory
    {
        /** @var EvaluationCategory */
        return $this->repo->update($category, $data);
    }

    public function delete(EvaluationCategory $category): void
    {
        $this->repo->delete($category);
    }
}
