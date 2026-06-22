<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\CourseSession;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface CourseSessionRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateForCourse(Course $course, int $perPage, ?int $sectionId): LengthAwarePaginator;
    public function createForCourse(Course $course, array $data): CourseSession;
    public function findWithSection(int $id): CourseSession;
}
