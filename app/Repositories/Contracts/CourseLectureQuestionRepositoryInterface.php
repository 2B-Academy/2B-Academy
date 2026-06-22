<?php

namespace App\Repositories\Contracts;

use Illuminate\Pagination\LengthAwarePaginator;

interface CourseLectureQuestionRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateFiltered(int $perPage, array $filters): LengthAwarePaginator;
}
