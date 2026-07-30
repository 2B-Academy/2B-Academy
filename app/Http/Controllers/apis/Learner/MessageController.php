<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis\Learner;

use App\Http\Controllers\apis\ApiController;
use App\Models\Admin;
use App\Models\Conversation;
use App\Models\Instructor;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 🌐 LEARNER WEB — two-way Messages (Figma frames 841-42746 / 841-43294).
 * List conversations, read a thread, reply, start a new conversation, and an
 * unread badge count. Auth: per-user Sanctum (auth.user + role:User).
 */
final class MessageController extends ApiController
{
    private const RECIPIENT_TYPES = [
        'instructor' => Instructor::class,
        'admin'      => Admin::class,
        'user'       => User::class,
    ];

    public function __construct(private readonly MessageService $service) {}

    /** GET learner/profile/messages?role=all|instructors|admins */
    public function index(Request $request): JsonResponse
    {
        $conversations = $this->service->conversationsFor(
            $request->user(),
            $request->get('role'),
            (int) $request->get('per_page', 20),
        );

        // Return a plain array (the widget doesn't page) so the client always
        // receives an iterable `result` rather than a serialized paginator.
        return $this->success(__('messages.retrieved'), $conversations->getCollection()->values());
    }

    /** GET learner/profile/messages/unread-count */
    public function unreadCount(Request $request): JsonResponse
    {
        $total = $this->service->conversationsFor($request->user(), null, 1000)
            ->getCollection()
            ->sum('unread_count');

        return $this->success(__('messages.retrieved'), ['count' => (int) $total]);
    }

    /** GET learner/profile/messages/recipients — who the learner can message. */
    public function recipients(Request $request): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->service->recipientsFor($request->user()),
        );
    }

    /** GET learner/profile/messages/{conversation} */
    public function show(Request $request, Conversation $conversation): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->service->thread($request->user(), $conversation),
        );
    }

    /** POST learner/profile/messages/{conversation}/reply */
    public function reply(Request $request, Conversation $conversation): JsonResponse
    {
        $data = $request->validate(['body' => ['required', 'string', 'max:5000']]);

        $this->service->reply($request->user(), $conversation, $data['body']);

        return $this->success(
            __('messages.sent'),
            $this->service->thread($request->user(), $conversation->fresh()),
        );
    }

    /** POST learner/profile/messages — start a new conversation. */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'recipient_type' => ['required', 'string', 'in:instructor,admin,user'],
            'recipient_id'   => ['required', 'integer'],
            'course_id'      => ['nullable', 'integer'],
            'body'           => ['required', 'string', 'max:5000'],
        ]);

        $message = $this->service->start(
            $request->user(),
            self::RECIPIENT_TYPES[$data['recipient_type']],
            (int) $data['recipient_id'],
            isset($data['course_id']) ? (int) $data['course_id'] : null,
            $data['body'],
        );

        return $this->success(__('messages.sent'), [
            'conversation_id' => (int) $message->conversation_id,
        ]);
    }
}
