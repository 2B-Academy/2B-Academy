<?php

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{
    /**
     * Authenticate user via external HR System, sync local record, issue Sanctum token.
     *
     * Returns ['token' => string, 'user' => User] on success, null on failure.
     */
    public function login(string $email, string $password): ?array
    {
        $hrService = new HRSystemService();
        $result    = $hrService->getAccessToken($email, $password, true);

        if (!$result || !isset($result->employee)) {
            return null;
        }

        $employee = $result->employee;

        $user = User::updateOrCreate(
            ['system_id' => $employee->employeeId],
            [
                'name'            => $employee->name,
                'email'           => $employee->email,
                'phone'           => $employee->phone           ?? null,
                'machine_code'    => $employee->machineCode,
                'department_name' => $employee->departmentName,
            ]
        );

        $token = $user->createToken(
            'user-api-token',
            ['role:user'],
            Carbon::now()->addDays(30)
        )->plainTextToken;

        return ['token' => $token, 'user' => $user->load('roles')];
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
