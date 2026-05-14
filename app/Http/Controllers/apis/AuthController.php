<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AdminLoginRequest;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Resources\AdminResource;
use App\Http\Resources\UserResource;
use App\Models\Admin;
use App\Models\User;
use App\Services\AdminAuthService;
use App\Services\UserAuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends ApiController
{
    public function __construct(
        private readonly UserAuthService  $userAuthService,
        private readonly AdminAuthService $adminAuthService,
    ) {}

    // -------------------------------------------------------------------------
    // User Auth
    // -------------------------------------------------------------------------

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

    public function userLogout(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->userAuthService->logout($user, $request->bearerToken());

        return $this->success(__('messages.logout_success'));
    }

    public function userLogoutAll(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();
        $this->userAuthService->logoutAll($user);

        return $this->success(__('messages.logout_all_success'));
    }

    public function userMe(Request $request): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new UserResource($request->user()->load('roles')),
        );
    }

    // -------------------------------------------------------------------------
    // Admin Auth
    // -------------------------------------------------------------------------

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

    public function adminLogout(Request $request): JsonResponse
    {
        /** @var Admin $admin */
        $admin = $request->user();
        $this->adminAuthService->logout($admin, $request->bearerToken());

        return $this->success(__('messages.logout_success'));
    }

    public function adminMe(Request $request): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new AdminResource($request->user()->load('roles')),
        );
    }
}
