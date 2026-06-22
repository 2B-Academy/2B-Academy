<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
    ) {}

    public function list(
        int     $perPage     = 20,
        ?string $search      = null,
        ?string $role        = null,
        ?string $learnerType = null,
    ): LengthAwarePaginator {
        return $this->userRepository->paginateWithSearch(
            $perPage, $search, $role, $learnerType,
        );
    }

    public function findOrFail(int $id): User
    {
        return $this->userRepository->findOrFail($id);
    }

    public function getUserWithActivity(int $id): User
    {
        return $this->userRepository->findWithActivity($id);
    }

    /**
     * Create a manually entered user (not synced from HR system).
     */
    public function create(array $data): User
    {
        $systemId   = rand(1, 9_999_999);
        $machineCode = Str::upper(Str::random(4));

        while ($this->userRepository->findBySystemId((string) $systemId)) {
            $systemId = rand(1, 9_999_999);
        }

        $data['system_id']  = $systemId;
        $data['email']      = "{$systemId}@2b.com";
        $data['machine_code'] = $machineCode;

        return $this->userRepository->create($data);
    }

    public function update(User $user, array $data): User
    {
        return $this->userRepository->update($user, $data);
    }

    public function delete(User $user): bool
    {
        return $this->userRepository->delete($user);
    }
}
