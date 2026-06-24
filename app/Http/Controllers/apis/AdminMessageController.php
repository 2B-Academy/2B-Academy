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
        $user = $request->user();

        $messages = $this->service->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
            tab:     $request->get('tab', 'all'),
            adminId: $user instanceof \App\Models\Admin ? $user->id : null,
            userId:  $user instanceof \App\Models\User  ? $user->id : null,
        );

        return $this->paginated(
            __('messages.retrieved'),
            AdminMessageResource::collection($messages),
        );
    }

    /**
     * Recipient catalog for the compose dialog: the "Learners" group plus
     * one group per admin-guard role that has Learning-Operations access.
     */
    public function recipients(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->service->recipientCatalog(),
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
     * Create a new admin message, fanned out to the selected recipient
     * groups (learners + admin roles).
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'subject'          => ['required', 'string', 'max:191'],
            'body'             => ['required', 'string'],
            'groups'           => ['required', 'array', 'min:1'],
            'groups.*.type'    => ['required', 'in:learner,role'],
            'groups.*.all'     => ['nullable', 'boolean'],
            'groups.*.role_id' => ['nullable', 'integer'],
            'groups.*.ids'     => ['nullable', 'array'],
            'groups.*.ids.*'   => ['integer'],
        ]);

        $message = $this->service->create($data, $request->user()->id);

        // Reject empty fan-outs (e.g. groups that resolved to nobody).
        if ($message->recipients()->count() === 0) {
            $message->delete();

            return response()->json(
                ['message' => __('validation.required', ['attribute' => 'recipients'])],
                422,
            );
        }

        return $this->created(
            __('messages.created'),
            new AdminMessageResource($this->service->show($message)),
        );
    }

    /**
     * Mark a message as read for the authenticated user (recipient side).
     */
    public function markRead(Request $request, AdminMessage $message): JsonResponse
    {
        $this->service->markRead($message, $request->user());

        return $this->success(__('messages.updated'));
    }

    /**
     * Admin override: mark ALL recipients of a message as read.
     * Useful when recipients (e.g. instructors) have no self-service portal.
     */
    public function markAllRead(AdminMessage $message): JsonResponse
    {
        $updated = $this->service->markAllRead($message);

        return $this->success(__('messages.updated'), ['updated' => $updated]);
    }
}
