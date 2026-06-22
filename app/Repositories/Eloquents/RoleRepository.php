<?php

namespace App\Repositories\Eloquents;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

class RoleRepository implements RoleRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator
    {
        return Role::with('permissions')
            ->when($search, fn ($q) => $q->where('name', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return Role::orderBy('name')->get();
    }

    public function findOrFail(int $id): Role
    {
        return Role::with('permissions')->findOrFail($id);
    }

    public function create(string $name, string $guardName = 'admin'): Role
    {
        return Role::create(['name' => $name, 'guard_name' => $guardName]);
    }

    public function update(Role $role, string $name): Role
    {
        $role->update(['name' => $name]);
        return $role->fresh();
    }

    public function delete(Role $role): void
    {
        $role->delete();
    }
}
