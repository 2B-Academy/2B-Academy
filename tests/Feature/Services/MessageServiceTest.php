<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Events\MessageSent;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Instructor;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Regression coverage for the "New Message duplicates an existing thread"
 * bug: a person can hold both an Instructor and an Admin account under the
 * same email (dashboard cross-entity convention). A message sent under one
 * of those accounts must land in the thread started under the other.
 */
class MessageServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_reply_under_a_sibling_identity_reuses_the_same_conversation(): void
    {
        $email = 'shared-identity@example.com';

        $admin      = Admin::factory()->create(['email' => $email]);
        $instructor = Instructor::create(['name' => 'Same Person', 'email' => $email]);
        $learner    = User::factory()->create();

        $service = app(MessageService::class);

        // Learner messages the instructor account first.
        $first = $service->start($learner, Instructor::class, $instructor->id, null, 'Hi mr Ezz');

        // The admin account (same physical person, same email) later
        // composes a fresh "New Message" to the same learner.
        $second = $service->start($admin, User::class, $learner->id, null, 'Hi Azoz');

        $this->assertSame(1, Conversation::count());
        $this->assertSame($first->conversation_id, $second->conversation_id);
    }

    public function test_reply_broadcasts_to_the_conversation_and_every_participant_identity(): void
    {
        Event::fake([MessageSent::class]);

        $instructor = Instructor::create(['name' => 'Some Instructor', 'email' => 'instructor@example.com']);
        $learner    = User::factory()->create();

        $service      = app(MessageService::class);
        $first        = $service->start($learner, Instructor::class, $instructor->id, null, 'Hi');
        $conversation = Conversation::findOrFail($first->conversation_id);

        $service->reply($instructor, $conversation, 'reply from instructor');

        Event::assertDispatched(MessageSent::class, function (MessageSent $event) use ($conversation, $instructor, $learner) {
            return $event->conversationId === $conversation->id
                && $event->payload['body'] === 'reply from instructor'
                && $event->payload['sender_id'] === $instructor->id
                && in_array('identity.Instructor.' . $instructor->id, $event->channels, true)
                && in_array('identity.User.' . $learner->id, $event->channels, true);
        });
    }
}
