<?php

namespace App\Repositories\Eloquents;

use App\Models\PublicNotification;
use App\Models\PublicNotificationUser;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationRepository extends BaseRepository implements NotificationRepositoryInterface
{
    public function __construct(PublicNotification $model)
    {
        parent::__construct($model);
    }

    public function paginateLatest(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('id')->paginate($perPage);
    }

    public function findWithUsers(int $id): PublicNotification
    {
        return $this->model->newQuery()->with('users')->findOrFail($id);
    }

    public function insertUserRecords(int $notificationId, array $userCodes): void
    {
        $rows = array_map(fn ($code) => [
            'public_notification_id' => $notificationId,
            'user_code'              => $code,
            'created_at'             => now(),
            'updated_at'             => now(),
        ], $userCodes);

        PublicNotificationUser::insert($rows);
    }
}
