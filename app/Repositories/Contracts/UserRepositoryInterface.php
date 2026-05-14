<?php

namespace App\Repositories\Contracts;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator;

    public function findBySystemId(string $systemId): ?User;

    public function updateOrCreateBySystemId(string $systemId, array $data): User;
}
