<?php

namespace App\Services;

use App\Http\Traits\TracksLastActive;
use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class UserAuthService
{
    use TracksLastActive;

    public function __construct(
        private readonly UserRepositoryInterface $userRepo
    ) {}

    public function login(string $identifier, string $password): ?array
    {
        // The "email" field may carry an email, a machine code (e.g. 2394)
        // or a system id. Resolve it to the matching local account so both
        // local-password and HR login work regardless of which one is typed.
        $localUser = $this->resolveLocalUser($identifier);

        // Local-first: honour a password set via the admin dashboard so
        // dashboard-managed accounts can sign in without an HR record.
        $localHash = $localUser?->getAttribute('password');
        if ($localUser && !empty($localHash) && Hash::check($password, $localHash)) {
            $token = $localUser->createToken(
                'user-api-token',
                ['role:user'],
                Carbon::now()->addDays(30)
            )->plainTextToken;

            $this->stampLastActive($localUser, force: true);
            $this->stampLastActiveByEmail($localUser->email ?? $identifier);

            return ['token' => $token, 'user' => $this->userRepo->findWithRoles($localUser->id)];
        }

        // HR expects the real email — fall back to the resolved account's
        // email so a machine code / system id still authenticates.
        $hrEmail   = $localUser->email ?? $identifier;
        $hrService = new HRSystemService();
        $result    = $hrService->getAccessToken($hrEmail, $password, true);

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

        // Register activity on every successful login — across all tables
        // sharing this email so an instructor row is stamped too.
        $this->stampLastActive($user, force: true);
        $this->stampLastActiveByEmail($user->email ?? $hrEmail);

        return ['token' => $token, 'user' => $this->userRepo->findWithRoles($user->id)];
    }

    /**
     * Resolve a login identifier (email, machine code, or system id) to a
     * local user so both local-password and HR login work whichever one a
     * person types into the "email" field.
     */
    private function resolveLocalUser(string $identifier): ?User
    {
        $identifier = trim($identifier);
        if ($identifier === '') {
            return null;
        }

        return User::query()
            ->where('email', $identifier)
            ->orWhere('machine_code', $identifier)
            ->when(ctype_digit($identifier), fn ($q) => $q->orWhere('system_id', (int) $identifier))
            ->first();
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
