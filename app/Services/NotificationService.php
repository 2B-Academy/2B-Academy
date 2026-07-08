<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\Instructor;
use App\Models\PublicNotification;
use App\Models\User;
use App\Notifications\BroadcastNotification;
use App\Repositories\Contracts\NotificationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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

        // Deliver a per-recipient in-app copy into the unified `notifications`
        // table so the composed broadcast shows up in each recipient's
        // personal feed alongside the event-driven notifications — with its
        // own read/unread state. This is additive to (not a replacement for)
        // the HR push + public_notification_users bookkeeping above.
        $this->deliverInAppRecords($notification, $userCodes, $instructorIds);

        return $notification;
    }

    /**
     * Fan a broadcast out into per-user rows on the `notifications` table.
     *
     *   - for_public  → every Admin + every learner (User) receives a copy
     *                   (admins are in-app consumers; learners for the mobile
     *                   / future in-app feed).
     *   - targeted    → only the addressed learners (by machine_code) and
     *                   instructors receive a copy.
     *
     * Uses a chunked bulk insert rather than per-model ->notify() so an
     * "all users" broadcast stays a handful of queries instead of thousands.
     *
     * @param  array<int, string>  $userCodes
     * @param  array<int, int>     $instructorIds
     */
    private function deliverInAppRecords(PublicNotification $notification, array $userCodes, array $instructorIds): void
    {
        $data = (new BroadcastNotification($notification))->payload();

        /** @var array<int, array{0: class-string, 1: int}> $recipients */
        $recipients = [];

        if ($notification->for_public) {
            foreach (Admin::query()->pluck('id') as $id) {
                $recipients[] = [Admin::class, (int) $id];
            }
            foreach (User::query()->pluck('id') as $id) {
                $recipients[] = [User::class, (int) $id];
            }
        } else {
            if (!empty($userCodes)) {
                $ids = User::query()
                    ->whereIn('machine_code', array_values(array_unique($userCodes)))
                    ->pluck('id');
                foreach ($ids as $id) {
                    $recipients[] = [User::class, (int) $id];
                }
            }
            if (!empty($instructorIds)) {
                $ids = Instructor::query()
                    ->whereIn('id', array_values(array_unique($instructorIds)))
                    ->pluck('id');
                foreach ($ids as $id) {
                    $recipients[] = [Instructor::class, (int) $id];
                }
            }
        }

        if ($recipients === []) {
            return;
        }

        $now     = now();
        $encoded = json_encode($data);

        $rows = array_map(static fn (array $r): array => [
            'id'              => (string) Str::uuid(),
            'type'            => BroadcastNotification::class,
            'notifiable_type' => $r[0],
            'notifiable_id'   => $r[1],
            'data'            => $encoded,
            'read_at'         => null,
            'created_at'      => $now,
            'updated_at'      => $now,
        ], $recipients);

        foreach (array_chunk($rows, 500) as $chunk) {
            DB::table('notifications')->insert($chunk);
        }
    }
}
