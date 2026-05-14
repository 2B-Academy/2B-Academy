<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use Illuminate\Database\Eloquent\Collection;

interface CourseSectionRepositoryInterface extends BaseRepositoryInterface
{
    public function allForCourse(Course $course): Collection;
    public function syncForCourse(Course $course, array $sections): void;
}
