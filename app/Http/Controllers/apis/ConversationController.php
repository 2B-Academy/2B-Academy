<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis;

use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Instructor;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Canonical two-way messaging API — the SINGLE store shared by the learner
 * website widget and the admin/instructor dashboard inbox. Principal-agnostic:
 * `$request->user()` may be a User (learner), Admin, or Instructor, and
 * MessageService resolves all same-email identities. This is what unifies the
 * two previously-disconnected surfaces onto one conversation thread.
 */
final class ConversationController extends ApiController
{
    /** Compose recipient_type → model class. */
    private const TYPES = [
        'instructor' => Instructor::class,
        'admin'      => Admin::class,
        'learner'    => User::class,
        'user'       => User::class,
    ];

    public function __construct(private readonly MessageService $service) {}

    /** GET conversations?role=all|instructors|admins|learners&tab=all|unread|received|sent */
    public function index(Request $request): JsonResponse
    {
        $conversations = $this->service->conversationsFor(
            $request->user(),
            $request->get('role'),
            (int) $request->get('per_page', 30),
            $request->get('tab'),
        );

        return $this->success(__('messages.retrieved'), $conversations->getCollection()->values());
    }

    /** GET conversations/unread-count */
    public function unreadCount(Request $request): JsonResponse
    {
        $total = $this->service->conversationsFor($request->user(), null, 1000)
            ->getCollection()
            ->sum('unread_count');

        return $this->success(__('messages.retrieved'), ['count' => (int) $total]);
    }

    /** GET conversations/recipients */
    public function recipients(Request $request): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->recipientsFor($request->user()));
    }

    /** GET conversations/{conversation} */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->service->thread($request->user(), $conversation));
    }

    /** POST conversations/{conversation}/reply */
    public function reply(Request $request, Conversation $conversation): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $this->service->reply($request->user(), $conversation, $data['body']);

        return $this->success(__('messages.sent'), $this->service->thread($request->user(), $conversation->fresh()));
    }

    /** POST conversations — start a single new conversation. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_type' => ['required', 'string', 'in:instructor,admin,learner,user'],
            'recipient_id'   => ['required', 'integer'],
            'course_id'      => ['nullable', 'integer'],
            'subject'        => ['nullable', 'string', 'max:191'],
            'body'           => ['required', 'string', 'max:5000'],
        ]);

        $message = $this->service->start(
            $request->user(),
            self::TYPES[$data['recipient_type']],
            (int) $data['recipient_id'],
            isset($data['course_id']) ? (int) $data['course_id'] : null,
            $data['body'],
            $data['subject'] ?? null,
        );

        return $this->success(__('messages.sent'), ['conversation_id' => (int) $message->conversation_id]);
    }

    /**
     * POST conversations/bulk — fan a message out to many recipients (the
     * dashboard "send to selected learners / whole role" broadcast), each as
     * its own two-way conversation.
     */
    public function bulk(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject'            => ['nullable', 'string', 'max:191'],
            'body'               => ['required', 'string', 'max:5000'],
            'recipients'         => ['required', 'array', 'min:1'],
            'recipients.*.type'  => ['required', 'string', 'in:instructor,admin,learner,user'],
            'recipients.*.id'    => ['required', 'integer'],
        ]);

        $recipients = collect($data['recipients'])
            ->map(fn ($r) => ['type' => self::TYPES[$r['type']], 'id' => (int) $r['id']])
            ->all();

        $count = $this->service->startMany(
            $request->user(),
            $recipients,
            $data['subject'] ?? null,
            $data['body'],
        );

        return $this->success(__('messages.sent'), ['count' => $count]);
    }
}
