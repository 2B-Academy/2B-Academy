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

    /**
     * @param  array<int, string>  $userCodes     HR machine codes for learner push targeting
     * @param  array<int, int>     $instructorIds Instructor PKs — stored in DB only, no HR push
     *                                            (instructors are not HR employees and have no
     *                                            device tokens in the HR push pipeline)
     */
    public function create(array $data, array $userCodes = [], array $instructorIds = []): PublicNotification
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
        } else {
            // Learner codes — store and push via HR pipeline
            if (!empty($userCodes)) {
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

            // Instructor IDs — store as `instr:<id>` codes for DB record keeping.
            // No push: instructors are LMS-only accounts without HR device tokens.
            if (!empty($instructorIds)) {
                $instrCodes = array_map(
                    static fn (int $id): string => 'instr:' . $id,
                    array_unique($instructorIds),
                );
                $this->repo->insertUserRecords($notification->id, $instrCodes);
            }
        }

        return $notification;
    }
}
