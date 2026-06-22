<?php

namespace App\Repositories\Contracts;

use App\Models\Admin;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AdminRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateWithSearch(int $perPage, ?string $search): LengthAwarePaginator;
    public function findWithRoles(int $id): Admin;
    public function findByEmail(string $email): ?Admin;
}
