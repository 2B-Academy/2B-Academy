<?php

namespace App\Services;

use App\Models\Admin;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    /**
     * Authenticate admin against the local admins table, issue Sanctum token.
     *
     * Returns ['token' => string, 'admin' => Admin] on success, null on failure.
     */
    public function login(string $email, string $password): ?array
    {
        $admin = Admin::where('email', $email)->first();

        if (!$admin || !Hash::check($password, $admin->password)) {
            return null;
        }

        $token = $admin->createToken(
            'admin-api-token',
            ['role:admin'],
            Carbon::now()->addDays(30)
        )->plainTextToken;

        return ['token' => $token, 'admin' => $admin->load('roles')];
    }

    public function logout(Admin $admin, string $rawToken): void
    {
        $tokenHash = hash('sha256', $rawToken);
        $admin->tokens()->where('token', $tokenHash)->delete();
    }

    public function logoutAll(Admin $admin): void
    {
        $admin->tokens()->delete();
    }
}
