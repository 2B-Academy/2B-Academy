<?php

namespace App\Services;

use App\Models\AdminMessage;
use App\Models\AdminMessageRecipient;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class AdminMessageService
{
    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return AdminMessage::query()
            ->with('admin:id,name')
            ->withCount([
                'recipients as total_recipients',
                'recipients as read_count' => fn ($q) => $q->whereNotNull('read_at'),
            ])
            ->when($search, fn ($q) => $q->where(function ($q2) use ($search) {
                $q2->where('subject', 'LIKE', "%{$search}%")
                   ->orWhere('body', 'LIKE', "%{$search}%");
            }))
            ->latest()
            ->paginate($perPage);
    }

    public function show(AdminMessage $message): AdminMessage
    {
        return $message->load([
            'admin:id,name',
            'recipients.user:id,name',
        ])->loadCount([
            'recipients as total_recipients',
            'recipients as read_count' => fn ($q) => $q->whereNotNull('read_at'),
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

            $now = now();
            $recipients = array_map(
                static fn (int $userId) => [
                    'admin_message_id' => $message->id,
                    'user_id'          => $userId,
                    'read_at'          => null,
                    'created_at'       => $now,
                    'updated_at'       => $now,
                ],
                $data['recipient_ids'],
            );

            AdminMessageRecipient::insert($recipients);

            return $message;
        });
    }

    public function markRead(AdminMessage $message, int $userId): void
    {
        AdminMessageRecipient::query()
            ->where('admin_message_id', $message->id)
            ->where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }
}
