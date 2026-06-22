<?php

namespace App\Services;

use App\Models\Evaluation;
use App\Repositories\Contracts\EvaluationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EvaluationCrudService
{
    public function __construct(
        private readonly EvaluationRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage = 20, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator
    {
        return $this->repo->paginateWithFilters($perPage, $search, $categoryId);
    }

    public function find(int $id): Evaluation
    {
        return $this->repo->findOrFail($id);
    }

    public function findWithCategory(int $id): Evaluation
    {
        return $this->repo->findWithCategory($id);
    }

    public function create(array $data): Evaluation
    {
        $data['is_required'] = (bool) ($data['is_required'] ?? true);
        return $this->repo->create($data);
    }

    public function update(Evaluation $evaluation, array $data): Evaluation
    {
        if (isset($data['is_required'])) {
            $data['is_required'] = (bool) $data['is_required'];
        }
        $this->repo->update($evaluation, $data);
        return $this->repo->findWithCategory($evaluation->id);
    }

    public function delete(Evaluation $evaluation): void
    {
        $this->repo->delete($evaluation);
    }
}
