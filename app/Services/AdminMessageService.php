<?php

namespace App\Services;

use App\Models\AdminMessage;
use App\Models\AdminMessageRecipient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminMessageService
{
    public function list(
        int     $perPage  = 15,
        ?string $search   = null,
        ?string $tab      = 'all',
        ?int    $adminId  = null,
        ?int    $userId   = null,
    ): LengthAwarePaginator {
        return AdminMessage::query()
            ->with('admin:id,name')
            ->withCount([
                'recipients as total_recipients',
                'recipients as read_count'              => fn ($q) => $q->whereNotNull('read_at'),
                'recipients as learner_recipients_count'    => fn ($q) => $q->whereNotNull('user_id'),
                'recipients as instructor_recipients_count' => fn ($q) => $q->whereNotNull('instructor_id'),
            ])
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('subject', 'LIKE', "%{$search}%")
                   ->orWhere('body', 'LIKE', "%{$search}%");
            }))
            // Tab filtering
            ->when($tab === 'sent' && $adminId, fn ($q) => $q->where('admin_id', $adminId))
            ->when($tab === 'unread', fn ($q) =>
                $q->whereHas('recipients')
                  ->whereColumn(
                      DB::raw('(SELECT COUNT(*) FROM admin_message_recipients WHERE admin_message_id = admin_messages.id AND read_at IS NULL)'),
                      '>',
                      DB::raw('0')
                  )
            )
            ->when($tab === 'received' && $userId, fn ($q) =>
                $q->whereHas('recipients', fn ($r) => $r->where('user_id', $userId))
            )
            ->latest()
            ->paginate($perPage);
    }

    public function show(AdminMessage $message): AdminMessage
    {
        return $message->load([
            'admin:id,name',
            'recipients.user:id,name',
            'recipients.instructor:id,name',
        ])->loadCount([
            'recipients as total_recipients',
            'recipients as read_count'              => fn ($q) => $q->whereNotNull('read_at'),
            'recipients as learner_recipients_count'    => fn ($q) => $q->whereNotNull('user_id'),
            'recipients as instructor_recipients_count' => fn ($q) => $q->whereNotNull('instructor_id'),
        ]);
    }

    public function create(array $data, int $adminId): AdminMessage
    {
        return DB::transaction(function () use ($data, $adminId) {
            /** @var AdminMessage $message */
            $message = AdminMessage::create([
                'admin_id' => $adminId,
                'subject'  => $data['subject'],
                'body'     => $data['body'],
            ]);

            $now  = now();
            $rows = [];

            foreach ($data['recipient_ids'] ?? [] as $userId) {
                $rows[] = [
                    'admin_message_id' => $message->id,
                    'user_id'          => $userId,
                    'instructor_id'    => null,
                    'read_at'          => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            foreach ($data['instructor_ids'] ?? [] as $instructorId) {
                $rows[] = [
                    'admin_message_id' => $message->id,
                    'user_id'          => null,
                    'instructor_id'    => $instructorId,
                    'read_at'          => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ];
            }

            if (!empty($rows)) {
                AdminMessageRecipient::insert($rows);
            }

            return $message;
        });
    }

    /**
     * Mark a message as read for the authenticated entity.
     * Supports both learner (User) and instructor (Instructor) recipients.
     */
    public function markRead(AdminMessage $message, \Illuminate\Database\Eloquent\Model $reader): void
    {
        $column = $reader instanceof \App\Models\Instructor ? 'instructor_id' : 'user_id';

        AdminMessageRecipient::query()
            ->where('admin_message_id', $message->id)
            ->where($column, $reader->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Admin override: mark ALL recipients of a message as read.
     * Used when recipients (e.g. instructors) have no self-service read portal.
     */
    public function markAllRead(AdminMessage $message): int
    {
        return AdminMessageRecipient::query()
            ->where('admin_message_id', $message->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
