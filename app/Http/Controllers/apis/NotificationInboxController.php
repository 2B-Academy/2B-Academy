<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\NotificationInboxResource;
use App\Models\Admin;
use App\Models\Instructor;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use OpenApi\Annotations as OA;

/**
 * "My notifications" inbox for the event-driven notification system
 * (pending grading, rating drops, assignment completion, course
 * assignment, cohort creation). Separate from `NotificationController`,
 * which manages the admin-composed broadcast/announcement tool.
 */
class NotificationInboxController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/notifications/mine",
     *     tags={"Notifications"},
     *     summary="List the authenticated principal's own notifications (paginated).",
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
     *                     @OA\Items(ref="#/components/schemas/NotificationInbox")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 20);
        $notifications = $this->feedQuery($request)
            ->latest()
            ->paginate($perPage);

        return $this->paginated(__('messages.retrieved'), NotificationInboxResource::collection($notifications));
    }

    /**
     * @OA\Get(
     *     path="/notifications/mine/unread-count",
     *     tags={"Notifications"},
     *     summary="Unread notification count for the authenticated principal.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Unread count",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", @OA\Property(property="count", type="integer")))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function unreadCount(Request $request): JsonResponse
    {
        $count = $this->feedQuery($request)->whereNull('read_at')->count();

        return $this->success(__('messages.retrieved'), ['count' => $count]);
    }

    /**
     * @OA\Post(
     *     path="/notifications/mine/{id}/read",
     *     tags={"Notifications"},
     *     summary="Mark a single notification as read.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/NotificationInbox"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function markRead(Request $request, string $id): JsonResponse
    {
        $notification = $this->feedQuery($request)->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        return $this->success(__('messages.updated'), new NotificationInboxResource($notification));
    }

    /**
     * @OA\Post(
     *     path="/notifications/mine/read-all",
     *     tags={"Notifications"},
     *     summary="Mark every unread notification as read for the authenticated principal.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(response=200, description="Updated", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function markAllRead(Request $request): JsonResponse
    {
        $this->feedQuery($request)->whereNull('read_at')->update(['read_at' => now()]);

        return $this->success(__('messages.updated'));
    }

    /**
     * Base query for the authenticated person's feed across every identity
     * row they own.
     *
     * A single human can exist in the `users`, `instructors` and `admins`
     * tables at once, linked by a shared email (the platform's cross-entity
     * identity convention — see UserAuthService + TracksLastActive). An
     * instructor has no login of their own, so they sign in as a User (HR
     * login); notifications addressed to their `Instructor` row must still
     * surface in that session. We therefore scope the feed to the principal
     * PLUS every same-email row in the other identity tables.
     */
    private function feedQuery(Request $request): Builder
    {
        $identities = $this->identities($request);

        return DatabaseNotification::query()->where(function (Builder $q) use ($identities) {
            foreach ($identities as [$type, $id]) {
                $q->orWhere(function (Builder $inner) use ($type, $id) {
                    $inner->where('notifiable_type', $type)->where('notifiable_id', $id);
                });
            }
        });
    }

    /**
     * Resolve every (notifiable_type, notifiable_id) pair that belongs to
     * the authenticated person: their own principal row, plus any
     * user/instructor/admin row sharing their email.
     *
     * @return array<int, array{0: class-string, 1: int|string}>
     */
    private function identities(Request $request): array
    {
        $principal = $request->user();

        /** @var array<int, array{0: class-string, 1: int|string}> $identities */
        $identities = [[$principal::class, $principal->getKey()]];

        $email = $principal->email ?? null;
        if ($email) {
            foreach ([User::class, Instructor::class, Admin::class] as $model) {
                foreach ($model::query()->where('email', $email)->pluck('id') as $id) {
                    $identities[] = [$model, $id];
                }
            }
        }

        return array_values(array_unique($identities, SORT_REGULAR));
    }

    /**
     * Admin oversight — inspect what an Instructor has received. Instructors
     * have no authentication in this system yet, so they cannot self-serve
     * this endpoint; this lets Admins verify delivery in the meantime.
     *
     * @OA\Get(
     *     path="/instructors/{instructor}/notifications",
     *     tags={"Notifications"},
     *     summary="List an instructor's notifications (admin oversight).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="instructor", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
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
     *                     @OA\Items(ref="#/components/schemas/NotificationInbox")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function forInstructor(Request $request, Instructor $instructor): JsonResponse
    {
        $perPage = (int) $request->get('per_page', 20);
        $notifications = $instructor->notifications()->paginate($perPage);

        return $this->paginated(__('messages.retrieved'), NotificationInboxResource::collection($notifications));
    }
}
