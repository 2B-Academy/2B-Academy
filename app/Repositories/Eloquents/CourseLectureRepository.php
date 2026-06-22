<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Repositories\Contracts\CourseLectureRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseLectureRepository extends BaseRepository implements CourseLectureRepositoryInterface
{
    public function __construct(CourseLecture $model)
    {
        parent::__construct($model);
    }

    public function sectionsWithLecturesForCourse(Course $course): Collection
    {
        return $course->sections()->with('lectures')->orderBy('id')->get();
    }

    public function createForCourse(Course $course, array $data): CourseLecture
    {
        return $course->lectures()->create($data);
    }
}
