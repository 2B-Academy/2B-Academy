<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;

class UserAuthService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {}

    public function login(string $email, string $password): ?array
    {
        $hrService = new HRSystemService();
        $result    = $hrService->getAccessToken($email, $password, true);

        if (!$result || !isset($result->employee)) {
            if ($hrService->lastError === 'unreachable') {
                abort(response()->json([
                    'status'  => 'error',
                    'message' => 'HR service is currently unreachable. Please try again later.',
                ], 503));
            }
            return null;
        }

        $employee = $result->employee;

        $user = $this->userRepo->updateOrCreateBySystemId($employee->employeeId, [
            'name'            => $employee->name,
            'email'           => $employee->email,
            'phone'           => $employee->phone           ?? null,
            'machine_code'    => $employee->machineCode,
            'department_name' => $employee->departmentName,
        ]);

        $token = $user->createToken(
            'user-api-token',
            ['role:user'],
            Carbon::now()->addDays(30)
        )->plainTextToken;

        return ['token' => $token, 'user' => $this->userRepo->findWithRoles($user->id)];
    }

    public function getWithRoles(User $user): User
    {
        return $this->userRepo->findWithRoles($user->id);
    }

    public function logout(User $user, string $rawToken): void
    {
        $tokenHash = hash('sha256', $rawToken);
        $user->tokens()->where('token', $tokenHash)->delete();
    }

    public function logoutAll(User $user): void
    {
        $user->tokens()->delete();
    }
}
