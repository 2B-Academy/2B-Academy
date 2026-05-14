<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class BaseRepository implements BaseRepositoryInterface
{
    public function __construct(protected Model $model) {}

    public function all(): Collection
    {
        return $this->model->newQuery()->latest()->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->model->newQuery()->latest()->paginate($perPage);
    }

    public function findById(int|string $id): ?Model
    {
        return $this->model->newQuery()->find($id);
    }

    public function findOrFail(int|string $id): Model
    {
        return $this->model->newQuery()->findOrFail($id);
    }

    public function create(array $data): Model
    {
        return $this->model->newQuery()->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model->fresh();
    }

    public function delete(Model $model): bool
    {
        return $model->delete();
    }
}
