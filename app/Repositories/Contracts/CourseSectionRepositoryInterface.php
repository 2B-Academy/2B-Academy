<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\CourseSection;
use Illuminate\Database\Eloquent\Collection;

interface CourseSectionRepositoryInterface extends BaseRepositoryInterface
{
    public function allForCourse(Course $course): Collection;
    public function syncForCourse(Course $course, array $sections): void;
    public function createForCourse(Course $course, array $data): CourseSection;
}
