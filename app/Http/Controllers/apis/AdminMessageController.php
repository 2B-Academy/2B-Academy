<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\AdminMessageResource;
use App\Models\AdminMessage;
use App\Services\AdminMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminMessageController extends ApiController
{
    public function __construct(private readonly AdminMessageService $service) {}

    /**
     * Paginated list of all admin messages (admin only).
     */
    public function index(Request $request): JsonResponse
    {
        $messages = $this->service->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminMessageResource::collection($messages),
        );
    }

    /**
     * Show a single message with recipients (admin only).
     */
    public function show(AdminMessage $message): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new AdminMessageResource($this->service->show($message)),
        );
    }

    /**
     * Create a new admin message with recipients (admin only).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject'        => ['required', 'string', 'max:255'],
            'body'           => ['required', 'string'],
            'recipient_ids'  => ['required', 'array', 'min:1'],
            'recipient_ids.*' => ['integer', 'exists:users,id'],
        ]);

        $message = $this->service->create($data, $request->user()->id);

        return $this->created(
            __('messages.created'),
            new AdminMessageResource($message->load('admin:id,name')->loadCount([
                'recipients as total_recipients',
                'recipients as read_count' => fn ($q) => $q->whereNotNull('read_at'),
            ])),
        );
    }

    /**
     * Mark a message as read for the authenticated user.
     */
    public function markRead(Request $request, AdminMessage $message): JsonResponse
    {
        $this->service->markRead($message, $request->user()->id);

        return $this->success(__('messages.updated'));
    }
}
