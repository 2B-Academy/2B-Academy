<?php

declare(strict_types=1);

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

/**
 * A conversation message just landed. Broadcast to the thread's live
 * viewers (`conversation.{id}`) and every participant's own inbox/unread
 * badge (`identity.{type}.{id}`) — see MessageService::broadcastMessage().
 *
 * Broadcasts synchronously (`ShouldBroadcastNow`, not `ShouldBroadcast`) —
 * a chat push queued behind `QUEUE_CONNECTION` would silently never arrive
 * unless a queue worker happens to be running. The extra round-trip to
 * Reverb inside the request is worth trading for that reliability.
 */
class MessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    /**
     * @param  array<int, string>    $channels  "identity.{type}.{id}" names
     * @param  array<string, mixed>  $payload   pre-shaped message data
     */
    public function __construct(
        public readonly int $conversationId,
        public readonly array $channels,
        public readonly array $payload,
    ) {}

    /** @return array<int, Channel> */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('conversation.' . $this->conversationId),
            ...array_map(fn (string $channel) => new PrivateChannel($channel), $this->channels),
        ];
    }

    public function broadcastAs(): string
    {
        return 'message.sent';
    }

    /** @return array<string, mixed> */
    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
