<?php

namespace App\Services;

use App\Models\PublicNotification;
use App\Models\PublicNotificationUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class NotificationService
{
    public function __construct(
        private readonly NotificationsApiService $pushService
    ) {}

    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return PublicNotification::orderByDesc('id')->paginate($perPage);
    }

    public function find(int $id): PublicNotification
    {
        return PublicNotification::with('users')->findOrFail($id);
    }

    public function create(array $data, array $userCodes = []): PublicNotification
    {
        $data['for_public'] = (bool) ($data['for_public'] ?? false);

        $notification = PublicNotification::create($data);

        if ($notification->for_public) {
            $this->pushService->sendNotificationsToAllUsers(
                $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'ar'),
                $notification->getTranslation('title', 'en') ?: $notification->getTranslation('title', 'ar'),
                $notification->getTranslation('body', 'en')  ?: $notification->getTranslation('body', 'ar'),
            );
        } elseif (!empty($userCodes)) {
            $codes = array_unique($userCodes);
            $rows  = array_map(fn ($code) => [
                'public_notification_id' => $notification->id,
                'user_code'              => $code,
                'created_at'             => now(),
                'updated_at'             => now(),
            ], $codes);
            PublicNotificationUser::insert($rows);

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
