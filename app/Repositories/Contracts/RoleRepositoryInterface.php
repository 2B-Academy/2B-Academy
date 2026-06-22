<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Spatie\Permission\Models\Role;

interface RoleRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator;
    public function all(): Collection;
    public function findOrFail(int $id): Role;
    public function create(string $name, string $guardName = 'admin'): Role;
    public function update(Role $role, string $name): Role;
    public function delete(Role $role): void;
}
