<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\PublicNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\PublicNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends ApiController
{
    public function __construct(private readonly NotificationService $service) {}

    public function index(Request $request): JsonResponse
    {
        $notifications = $this->service->paginate((int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $notifications);
    }

    public function show(PublicNotification $notification): JsonResponse
    {
        $notification->load('users');
        return $this->success(__('messages.retrieved'), new NotificationResource($notification));
    }

    public function store(PublicNotificationRequest $request): JsonResponse
    {
        $data      = $request->validated();
        $userCodes = $data['user_codes'] ?? [];
        unset($data['user_codes']);

        $notification = $this->service->create($data, $userCodes);
        return $this->created(__('messages.created'), new NotificationResource($notification));
    }
}
