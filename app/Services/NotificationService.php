<?php

namespace App\Services;

use App\Models\PublicNotification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationRepositoryInterface $repo,
        private readonly NotificationsApiService         $pushService
    ) {}

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return $this->repo->paginateLatest($perPage);
    }

    public function find(int $id): PublicNotification
    {
        return $this->repo->findWithUsers($id);
    }

    public function update(PublicNotification $notification, array $data): PublicNotification
    {
        $notification->update($data);
        return $notification->fresh();
    }

    public function delete(PublicNotification $notification): void
    {
        $notification->delete();
    }

    public function create(array $data, array $userCodes = []): PublicNotification
    {
        $data['for_public'] = (bool) ($data['for_public'] ?? false);

        /** @var PublicNotification $notification */
        $notification = $this->repo->create($data);

        if ($notification->for_public) {
            $this->pushService->sendNotificationsToAllUsers(
                $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'ar'),
                $notification->getTranslation('title', 'en') ?: $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'en')  ?: $notification->getTranslation('body', 'ar'),
            );
        } elseif (!empty($userCodes)) {
            $codes = array_unique($userCodes);
            $this->repo->insertUserRecords($notification->id, $codes);

            $this->pushService->sendNotificationsToSelectedUsers(
                $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'ar'),
                $notification->getTranslation('title', 'en') ?: $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'en')  ?: $notification->getTranslation('body', 'ar'),
                $codes,
            );
        }

        return $notification;
    }
}
