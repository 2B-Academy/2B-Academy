<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AdminLoginRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\UpdateAdminProfileRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use App\Services\AdminAuthService;
use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class AuthController extends ApiController
{
    public function __construct(
        private readonly UserAuthService  $userAuthService,
        private readonly AdminAuthService $adminAuthService,
    ) {}

    // -------------------------------------------------------------------------
    // User Auth
    // -------------------------------------------------------------------------

    /**
     * @OA\Post(
     *     path="/auth/user/login",
     *     tags={"Auth"},
     *     summary="User login (employee). Returns a Sanctum bearer token.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email",    type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged in",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="result",
     *                         type="object",
     *                         @OA\Property(property="token", type="string"),
     *                         @OA\Property(property="user",  ref="#/components/schemas/User")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function userLogin(LoginRequest $request): JsonResponse
    {
        $result = $this->userAuthService->login(
            $request->email,
            $request->password,
        );

        if (!$result) {
            return $this->error(__('messages.invalid_credentials'), 401);
        }

        return $this->success(__('messages.login_success'), [
            'token' => $result['token'],
            'user'  => new UserResource($result['user']),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/user/logout",
     *     tags={"Auth"},
     *     summary="Revoke the current user bearer token.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(response=200, description="Logged out", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function userLogout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->userAuthService->logout($user, $request->bearerToken());

        return $this->success(__('messages.logout_success'));
    }

    /**
     * @OA\Post(
     *     path="/auth/user/logout-all",
     *     tags={"Auth"},
     *     summary="Revoke ALL of this user's bearer tokens (all devices).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(response=200, description="Logged out from all devices", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function userLogoutAll(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->userAuthService->logoutAll($user);

        return $this->success(__('messages.logout_all_success'));
    }

    /**
     * @OA\Get(
     *     path="/auth/user/me",
     *     tags={"Auth"},
     *     summary="Get the authenticated user's profile (with roles).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current user",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/User"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function userMe(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        return $this->success(
            __('messages.retrieved'),
            new UserResource($this->userAuthService->getWithRoles($user)),
        );
    }

    /**
     * @OA\Put(
     *     path="/auth/user/profile",
     *     tags={"Auth"},
     *     summary="Update the authenticated user's profile.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name",  type="string", maxLength=255),
     *             @OA\Property(property="phone", type="string", maxLength=50, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/User"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function userUpdateProfile(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        $data = $request->validate([
            'name'  => 'sometimes|string|max:255',
            'phone' => 'sometimes|nullable|string|max:50',
        ]);

        $user->update($data);

        return $this->success(
            __('messages.updated'),
            new UserResource($this->userAuthService->getWithRoles($user->fresh())),
        );
    }

    // -------------------------------------------------------------------------
    // Admin Auth
    // -------------------------------------------------------------------------

    /**
     * @OA\Post(
     *     path="/auth/admin/login",
     *     tags={"Auth"},
     *     summary="Admin login. Returns a Sanctum bearer token bound to the admin guard.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email","password"},
     *             @OA\Property(property="email",    type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Logged in",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="result",
     *                         type="object",
     *                         @OA\Property(property="token", type="string"),
     *                         @OA\Property(property="admin", ref="#/components/schemas/Admin")
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $result = $this->adminAuthService->login(
            $request->email,
            $request->password,
        );

        if (!$result) {
            return $this->error(__('messages.invalid_credentials'), 401);
        }

        return $this->success(__('messages.login_success'), [
            'token' => $result['token'],
            'admin' => new AdminResource($result['admin']),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/auth/admin/logout",
     *     tags={"Auth"},
     *     summary="Revoke the current admin's bearer token.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(response=200, description="Logged out", @OA\JsonContent(ref="#/components/schemas/SuccessResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function adminLogout(Request $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();
        $this->adminAuthService->logout($admin, $request->bearerToken());

        return $this->success(__('messages.logout_success'));
    }

    /**
     * @OA\Get(
     *     path="/auth/admin/me",
     *     tags={"Auth"},
     *     summary="Get the authenticated admin's profile (with roles).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Current admin",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Admin"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function adminMe(Request $request): JsonResponse
    {
        /** @var \App\Models\Admin $admin */
        $admin = $request->user();
        return $this->success(
            __('messages.retrieved'),
            new AdminResource($this->adminAuthService->getWithRoles($admin)),
        );
    }

    /**
     * @OA\Put(
     *     path="/auth/admin/profile",
     *     tags={"Auth"},
     *     summary="Update the authenticated admin's profile.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name",     type="string", maxLength=255),
     *             @OA\Property(property="email",    type="string", format="email"),
     *             @OA\Property(property="password", type="string", format="password", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Admin"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function adminUpdateProfile(UpdateAdminProfileRequest $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();
        $admin->update($request->validated());

        return $this->success(
            __('messages.updated'),
            new AdminResource($this->adminAuthService->getWithRoles($admin->fresh())),
        );
    }
}
