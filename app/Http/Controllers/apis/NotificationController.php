<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\PublicNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\PublicNotification;
use App\Services\NotificationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class NotificationController extends ApiController
{
    public function __construct(private readonly NotificationService $service) {}

    /**
     * @OA\Get(
     *     path="/notifications",
     *     tags={"Notifications"},
     *     summary="List notifications (paginated, admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated notifications",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Notification")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $notifications = $this->service->paginate((int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $notifications);
    }

    /**
     * @OA\Get(
     *     path="/notifications/{notification}",
     *     tags={"Notifications"},
     *     summary="Show a notification (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="notification", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Notification",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Notification"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(PublicNotification $notification): JsonResponse
    {
        $notification = $this->service->find($notification->id);
        return $this->success(__('messages.retrieved'), new NotificationResource($notification));
    }

    /**
     * @OA\Post(
     *     path="/notifications",
     *     tags={"Notifications"},
     *     summary="Create and dispatch a notification (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","body"},
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="body",       ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="for_public", type="boolean", nullable=true, description="When true, send to all users."),
     *             @OA\Property(
     *                 property="user_codes",
     *                 type="array",
     *                 nullable=true,
     *                 description="Target specific users by employee code. Ignored when for_public=true.",
     *                 @OA\Items(type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Notification"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(PublicNotificationRequest $request): JsonResponse
    {
        $data      = $request->validated();
        $userCodes = $data['user_codes'] ?? [];
        unset($data['user_codes']);

        $notification = $this->service->create($data, $userCodes);
        return $this->created(__('messages.created'), new NotificationResource($notification));
    }

    /**
     * @OA\Put(
     *     path="/notifications/{notification}",
     *     tags={"Notifications"},
     *     summary="Update a notification (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="notification", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="body",       ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="for_public", type="boolean", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Notification"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(PublicNotificationRequest $request, PublicNotification $notification): JsonResponse
    {
        $data      = $request->validated();
        unset($data['user_codes']);

        $updated = $this->service->update($notification, $data);
        return $this->success(__('messages.updated'), new NotificationResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/notifications/{notification}",
     *     tags={"Notifications"},
     *     summary="Delete a notification (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="notification", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(PublicNotification $notification): JsonResponse
    {
        $this->service->delete($notification);
        return $this->deleted();
    }
}
