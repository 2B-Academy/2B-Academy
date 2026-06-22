<?php

namespace App\Services;

use App\Models\Admin;
use App\Repositories\Contracts\AdminRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class AdminAuthService
{
    public function __construct(
        private readonly AdminRepositoryInterface $repo
    ) {}

    public function login(string $email, string $password): ?array
    {
        $admin = $this->repo->findByEmail($email);

        if (!$admin || !Hash::check($password, $admin->password)) {
            return null;
        }

        $token = $admin->createToken(
            'admin-api-token',
            ['role:admin'],
            Carbon::now()->addDays(30)
        )->plainTextToken;

        return ['token' => $token, 'admin' => $this->repo->findWithRoles($admin->id)];
    }

    public function getWithRoles(Admin $admin): Admin
    {
        return $this->repo->findWithRoles($admin->id);
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
