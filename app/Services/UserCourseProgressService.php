<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserCourseProgressService
{
    public function paginate(int $perPage, ?int $courseId, ?int $groupId, ?int $userId): LengthAwarePaginator
    {
        $results = User::query()
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

        $results->getCollection()->transform(fn ($user) => $this->formatRow($user));

        return $results;
    }

    private function formatRow(User $user): array
    {
        $finalExam  = $user->exams->first();
        $evaluation = $user->evaluations->first();
        $progress   = ($finalExam || $evaluation) ? 100 : 0;

        return [
            'user'            => [
                'id'              => $user->id,
                'name'            => $user->name,
                'machine_code'    => $user->machine_code,
                'department_name' => $user->department_name,
            ],
            'course'          => [
                'id'          => $user->course_id,
                'title'       => $user->course_title,
                'course_type' => $user->course_type,
                'for_public'  => (bool) $user->for_public,
            ],
            'group_name'      => $user->group_name,
            'user_degree'     => $finalExam?->user_degree,
            'total_degree'    => $finalExam?->exam?->degree,
            'progress'        => $progress,
        ];
    }
}
