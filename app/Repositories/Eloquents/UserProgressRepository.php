<?php

namespace App\Repositories\Eloquents;

use App\Models\User;
use App\Repositories\Contracts\UserProgressRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserProgressRepository implements UserProgressRepositoryInterface
{
    public function paginate(int $perPage, ?int $courseId, ?int $groupId, ?int $userId): LengthAwarePaginator
    {
        return User::query()
            ->select([
                'users.id',
                'users.machine_code',
                'users.name',
                'users.department_name',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(courses.title, '$.ar')) AS course_title"),
                'courses.course_type',
                'courses.for_public',
                'courses.id AS course_id',
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(course_sections.name, '$.ar')) AS group_name"),
            ])
            ->join('users_courses', 'users.id', '=', 'users_courses.user_id')
            ->join('courses', 'courses.id', '=', 'users_courses.course_id')
            ->leftJoin('course_sections', 'course_sections.id', '=', 'users_courses.group_id')
            ->when($courseId, fn ($q) => $q->where('courses.id', $courseId))
            ->when($groupId,  fn ($q) => $q->where('users_courses.group_id', $groupId))
            ->when($userId,   fn ($q) => $q->where('users.id', $userId))
            ->with([
                'exams' => fn ($q) => $q->whereHas('exam', fn ($eq) => $eq->where('is_final', true))
                    ->whereNotNull('user_degree'),
                'evaluations',
            ])
            ->paginate($perPage);
    }
}
