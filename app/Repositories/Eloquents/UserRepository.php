<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{
    public function __construct(User $model)
    {
        parent::__construct($model);
    }

    public function paginateWithSearch(
        int     $perPage,
        ?string $search,
        ?string $role        = null,
        ?string $learnerType = null,
    ): LengthAwarePaginator {
        return $this->model->newQuery()
            // Eager-load roles so the admin UI can show a "learner" vs
            // "instructor" pill without firing a per-row query.
            ->with('roles:id,name')
            ->when($search, fn ($q) => $q->where(function ($inner) use ($search) {
                $inner->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%")
                    ->orWhere('machine_code', 'LIKE', "%{$search}%")
                    ->orWhere('department_name', 'LIKE', "%{$search}%");
            }))
            // `role=instructor` → only users with the Spatie 'instructor' role.
            // `role=learner`    → every user is a learner by default in this
            //                     LMS, so the filter is a no-op (we still
            //                     accept it so the UI param is harmless).
            ->when($role === 'instructor', fn ($q) =>
                $q->whereHas('roles', fn ($r) => $r->where('name', 'instructor'))
            )
            ->when($learnerType, fn ($q) => $q->where('learner_type', $learnerType))
            ->orderBy('system_id')
            ->paginate($perPage);
    }

    public function findBySystemId(string $systemId): ?User
    {
        return $this->model->newQuery()->where('system_id', $systemId)->first();
    }

    public function updateOrCreateBySystemId(string $systemId, array $data): User
    {
        return $this->model->newQuery()->updateOrCreate(
            ['system_id' => $systemId],
            $data,
        );
    }

    public function findWithRoles(int $id): User
    {
        return $this->model->newQuery()->with('roles')->findOrFail($id);
    }

    public function findWithActivity(int $id): User
    {
        return $this->model->newQuery()
            ->with([
                'roles:id,name',
                'courses.category:id,name',
                'ratings.course:id,title',
                'exams.course:id,title,certificate',
                'exams.exam:id,title,degree,is_final',
            ])
            ->findOrFail($id);
    }
}
