<?php

namespace App\Notifications;

use App\Models\PublicNotification;
use Illuminate\Notifications\Notification;

/**
 * Per-recipient in-app copy of an admin-composed broadcast/announcement
 * (the `public_notifications` composer flow). Delivered into the same
 * `notifications` table as the event-driven notifications so a single
 * per-user feed ("my notifications") surfaces both flows together.
 *
 * The bilingual title/body are snapshotted from the PublicNotification at
 * send time and stored per locale, matching the event-driven payload shape
 * consumed by NotificationInboxResource.
 */
class BroadcastNotification extends Notification
{
    public function __construct(
        private readonly PublicNotification $note,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * The stored `data` payload — exposed separately so the bulk fan-out
     * path (all-users broadcasts) can reuse it without instantiating a
     * database row per notifiable.
     *
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'type'     => 'broadcast',
            'title_en' => $this->note->getTranslation('title', 'en') ?: $this->note->getTranslation('title', 'ar'),
            'title_ar' => $this->note->getTranslation('title', 'ar') ?: $this->note->getTranslation('title', 'en'),
            'body_en'  => $this->note->getTranslation('body', 'en') ?: $this->note->getTranslation('body', 'ar'),
            'body_ar'  => $this->note->getTranslation('body', 'ar') ?: $this->note->getTranslation('body', 'en'),
            'meta'     => [
                'public_notification_id' => $this->note->id,
                'for_public'             => (bool) $this->note->for_public,
            ],
        ];
    }
}
