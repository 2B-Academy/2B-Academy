<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(
        int     $perPage,
        ?string $search,
        ?string $role        = null,
        ?string $learnerType = null,
    ): LengthAwarePaginator;

    public function findBySystemId(string $systemId): ?User;

    public function updateOrCreateBySystemId(string $systemId, array $data): User;

    public function findWithRoles(int $id): User;

    public function findWithActivity(int $id): User;
}
