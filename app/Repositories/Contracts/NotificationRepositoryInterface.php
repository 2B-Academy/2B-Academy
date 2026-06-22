<?php

namespace App\Repositories\Contracts;

use App\Models\PublicNotification;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface NotificationRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateLatest(int $perPage): LengthAwarePaginator;
    public function findWithUsers(int $id): PublicNotification;
    public function insertUserRecords(int $notificationId, array $userCodes): void;
}
