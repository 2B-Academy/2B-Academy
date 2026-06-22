<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserProgressRepositoryInterface
{
    public function paginate(int $perPage, ?int $courseId, ?int $groupId, ?int $userId): LengthAwarePaginator;
}
