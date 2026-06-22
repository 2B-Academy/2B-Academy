<?php

namespace App\Repositories\Contracts;

use App\Models\Course;
use App\Models\CourseLecture;
use Illuminate\Database\Eloquent\Collection;

interface CourseLectureRepositoryInterface extends BaseRepositoryInterface
{
    public function sectionsWithLecturesForCourse(Course $course): Collection;
    public function createForCourse(Course $course, array $data): CourseLecture;
}
