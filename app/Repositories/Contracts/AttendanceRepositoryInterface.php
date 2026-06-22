<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface AttendanceRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateFiltered(int $perPage, array $filters): LengthAwarePaginator;

    public function countForUserInSection(int $userId, int $sectionId): int;

    /**
     * Raw attendance rows for one (course, section) tuple, eagerly joined
     * with the user so the cohort rollup can group + count in PHP without
     * a follow-up N+1.
     *
     * @return \Illuminate\Support\Collection<int, object{
     *     id:int, user_id:int, course_id:int, section_id:int,
     *     session_id:?int, attended_on:string,
     *     user_name:?string, user_machine_code:?string, user_department:?string
     * }>
     */
    public function cohortRows(int $courseId, int $sectionId): \Illuminate\Support\Collection;
}
