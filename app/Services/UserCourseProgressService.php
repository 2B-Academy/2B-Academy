<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Contracts\UserProgressRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserCourseProgressService
{
    public function __construct(
        private readonly UserProgressRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage, ?int $courseId, ?int $groupId, ?int $userId): LengthAwarePaginator
    {
        $results = $this->repo->paginate($perPage, $courseId, $groupId, $userId);
        $results->getCollection()->transform(fn ($user) => $this->formatRow($user));
        return $results;
    }

    private function formatRow(User $user): array
    {
        $finalExam  = $user->exams->first();
        $evaluation = $user->evaluations->first();
        $progress   = ($finalExam || $evaluation) ? 100 : 0;

        return [
            'user'         => [
                'id'              => $user->id,
                'name'            => $user->name,
                'machine_code'    => $user->machine_code,
                'department_name' => $user->department_name,
            ],
            'course'       => [
                'id'          => $user->course_id,
                'title'       => $user->course_title,
                'course_type' => $user->course_type,
                'for_public'  => (bool) $user->for_public,
            ],
            'group_name'   => $user->group_name,
            'user_degree'  => $finalExam?->user_degree,
            'total_degree' => $finalExam?->exam?->degree,
            'progress'     => $progress,
        ];
    }
}
