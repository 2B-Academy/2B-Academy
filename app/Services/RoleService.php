<?php

namespace App\Services;

use App\Repositories\Contracts\RoleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->repo->paginateWithSearch($perPage, $search);
    }

    public function all(): Collection
    {
        return $this->repo->all();
    }

    public function find(int $id): Role
    {
        return $this->repo->findOrFail($id);
    }

    public function create(string $name, array $permissions = [], string $guardName = 'admin'): Role
    {
        $role = $this->repo->create($name, $guardName);
        $this->syncPermissions($role, $permissions);
        return $this->repo->findOrFail($role->id);
    }

    public function update(Role $role, string $name, array $permissions = []): Role
    {
        $role = $this->repo->update($role, $name);
        $this->syncPermissions($role, $permissions);
        return $this->repo->findOrFail($role->id);
    }

    public function delete(Role $role): void
    {
        $this->repo->delete($role);
    }

    /**
     * Spatie's `syncPermissions(['name', ...])` resolves names against the
     * *role's* guard. When a role's guard doesn't match where the requested
     * permissions live (e.g. an admin-panel UI sending `courses-create` to a
     * `web`-guard role), Spatie throws an unhelpful 500. We resolve the names
     * to model instances ourselves so we can:
     *   1. Skip Spatie's name→guard lookup entirely
     *   2. Detect cross-guard mismatches and return a clear 422 instead of 500
     *   3. Accept an empty list as "detach everything"
     */
    private function syncPermissions(Role $role, array $permissionNames): void
    {
        if (empty($permissionNames)) {
            $role->syncPermissions([]);
            return;
        }

        $resolved = Permission::query()
            ->whereIn('name', $permissionNames)
            ->where('guard_name', $role->guard_name)
            ->get();

        if ($resolved->isEmpty()) {
            throw new HttpResponseException(Response::json([
                'status'  => 'error',
                'message' => "None of the selected permissions exist for the `{$role->guard_name}` guard used by this role.",
                'errors'  => ['permissions' => ["No matching permissions found for guard `{$role->guard_name}`."]],
            ], 422));
        }

        $role->syncPermissions($resolved);
    }
}
