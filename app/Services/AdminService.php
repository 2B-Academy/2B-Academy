<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Hash;

class AdminService
{
    public function __construct(
        private readonly AdminRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage, ?string $search): LengthAwarePaginator
    {
        return $this->repo->paginateWithSearch($perPage, $search);
    }

    public function find(int $id): Admin
    {
        /** @var Admin */
        return $this->repo->findOrFail($id);
    }

    public function findWithRoles(int $id): Admin
    {
        return $this->repo->findWithRoles($id);
    }

    public function create(array $data): Admin
    {
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);
        $data['password'] = Hash::make($data['password']);

        /** @var Admin $admin */
        $admin = $this->repo->create($data);
        $admin->assignRole($role);

        return $this->repo->findWithRoles($admin->id);
    }

    public function update(Admin $admin, array $data): Admin
    {
        $role = $data['role'];
        unset($data['role'], $data['password_confirmation']);

        if (!empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        /** @var Admin $admin */
        $admin = $this->repo->update($admin, $data);
        $admin->syncRoles([$role]);

        return $this->repo->findWithRoles($admin->id);
    }

    public function delete(Admin $admin): void
    {
        $this->repo->delete($admin);
    }
}
