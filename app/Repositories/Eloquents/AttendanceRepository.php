<?php

namespace App\Repositories\Eloquents;

use App\Models\Attendance;
use App\Repositories\Contracts\AttendanceRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class AttendanceRepository extends BaseRepository implements AttendanceRepositoryInterface
{
    public function __construct(Attendance $model)
    {
        parent::__construct($model);
    }

    public function paginateFiltered(int $perPage, array $filters): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->when($filters['course_id'] ?? null, fn ($q, $v) => $q->where('course_id', $v))
            ->when($filters['user_id'] ?? null, fn ($q, $v) => $q->where('user_id', $v))
            ->when($filters['section_id'] ?? null, fn ($q, $v) => $q->where('section_id', $v))
            ->when($filters['from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v))
            ->latest()
            ->paginate($perPage);
    }

    public function countForUserInSection(int $userId, int $sectionId): int
    {
        return $this->model->newQuery()
            ->where('user_id', $userId)
            ->where('section_id', $sectionId)
            ->count();
    }

    public function cohortRows(int $courseId, int $sectionId): Collection
    {
        // One narrow query — no Eloquent hydration since we just need a
        // flat tuple per (user, session/day) for the in-memory rollup.
        // We left-join users instead of attendances.user_machine_code so
        // the cohort drawer reflects the *current* HR roster name even
        // when older rows were stamped with a snapshot value.
        return DB::table('attendances as a')
            ->leftJoin('users as u', 'u.id', '=', 'a.user_id')
            ->where('a.course_id',  $courseId)
            ->where('a.section_id', $sectionId)
            ->selectRaw('
                a.id              as id,
                a.user_id         as user_id,
                a.course_id       as course_id,
                a.section_id      as section_id,
                a.session_id      as session_id,
                DATE(a.created_at) as attended_on,
                COALESCE(u.name,             a.user_machine_code) as user_name,
                COALESCE(u.machine_code,     a.user_machine_code) as user_machine_code,
                COALESCE(u.department_name,  a.user_department)   as user_department
            ')
            ->get();
    }
}
