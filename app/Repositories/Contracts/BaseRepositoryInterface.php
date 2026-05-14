<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface BaseRepositoryInterface
{
    public function all(): Collection;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function findById(int|string $id): ?Model;

    public function findOrFail(int|string $id): Model;

    public function create(array $data): Model;

    public function update(Model $model, array $data): Model;

    public function delete(Model $model): bool;
}
