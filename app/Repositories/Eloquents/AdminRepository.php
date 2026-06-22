<?php

namespace App\Repositories\Eloquents;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AdminRepository extends BaseRepository implements AdminRepositoryInterface
{
    public function __construct(Admin $model)
    {
        parent::__construct($model);
    }

    public function findWithRoles(int $id): Admin
    {
        // Eager-load `roles.permissions` so the AdminResource can expose
        // the admin's effective `view-*` permissions without falling into
        // an N+1 every request.
        return $this->model->newQuery()
            ->with(['roles', 'roles.permissions'])
            ->findOrFail($id);
    }

    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->with('roles')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findByEmail(string $email): ?Admin
    {
        return $this->model->newQuery()->where('email', $email)->first();
    }
}
