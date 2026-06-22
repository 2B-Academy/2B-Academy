<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use OpenApi\Annotations as OA;

class UserController extends ApiController
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * @OA\Get(
     *     path="/users",
     *     tags={"Users"},
     *     summary="List users (paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="role", in="query", required=false,
     *         description="Filter by user role tab.",
     *         @OA\Schema(type="string", enum={"learner","instructor"})
     *     ),
     *     @OA\Parameter(
     *         name="learner_type", in="query", required=false,
     *         description="Filter learners by delivery preference.",
     *         @OA\Schema(type="string", enum={"online","offline","hybrid"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated users",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/User")
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
        $role = $request->filled('role') && in_array($request->get('role'), ['learner', 'instructor'], true)
            ? (string) $request->get('role')
            : null;

        $learnerType = $request->filled('learner_type')
            && in_array($request->get('learner_type'), ['online', 'offline', 'hybrid'], true)
                ? (string) $request->get('learner_type')
                : null;

        $users = $this->userService->list(
            perPage:     (int) $request->get('per_page', 20),
            search:      $request->get('search'),
            role:        $role,
            learnerType: $learnerType,
        );

        return $this->paginated(__('messages.retrieved'), UserResource::collection($users));
    }

    /**
     * @OA\Get(
     *     path="/users/{user}",
     *     tags={"Users"},
     *     summary="Show a user (with activity).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="User detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/User"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(User $user): JsonResponse
    {
        $user = $this->userService->getUserWithActivity($user->id);

        return $this->success(
            __('messages.retrieved'),
            new UserResource($user),
        );
    }

    /**
     * @OA\Post(
     *     path="/users",
     *     tags={"Users"},
     *     summary="Create a user (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", maxLength=255)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/User"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = $this->userService->create($data);

        return $this->created(
            __('messages.created'),
            new UserResource($user),
        );
    }

    /**
     * @OA\Put(
     *     path="/users/{user}",
     *     tags={"Users"},
     *     summary="Update a user (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name",            type="string", maxLength=255),
     *             @OA\Property(property="phone",           type="string", maxLength=50, nullable=true),
     *             @OA\Property(property="department_name", type="string", maxLength=255, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/User"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Request $request, User $user): JsonResponse
    {
        $data = $request->validate([
            'name'            => 'sometimes|string|max:255',
            'email'           => 'sometimes|nullable|email|max:255',
            'phone'           => 'sometimes|nullable|string|max:50',
            'department_name' => 'sometimes|nullable|string|max:255',
            'learner_type'    => 'sometimes|nullable|in:online,offline,hybrid',
        ]);

        $user = $this->userService->update($user, $data);

        return $this->success(__('messages.updated'), new UserResource($user));
    }

    /**
     * @OA\Delete(
     *     path="/users/{user}",
     *     tags={"Users"},
     *     summary="Delete a user (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="user", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(User $user): JsonResponse
    {
        $this->userService->delete($user);
        return $this->deleted();
    }

    /**
     * @OA\Get(
     *     path="/users/search",
     *     tags={"Users"},
     *     summary="Lightweight user list for select2 / dropdowns.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="q",
     *         in="query",
     *         required=false,
     *         description="Search term for filtering users by name/email.",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Users matching the query",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/User")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function search(Request $request): JsonResponse
    {
        $users = $this->userService->list(100, $request->get('q'));

        return $this->success(
            __('messages.retrieved'),
            UserResource::collection($users),
        );
    }
}
