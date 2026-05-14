<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\CourseLecture;

class CourseLectureService
{
    use HasFile;

    public function listForCourse(Course $course): \Illuminate\Database\Eloquent\Collection
    {
        return $course->sections()->with('lectures')->orderBy('id')->get();
    }

    public function create(Course $course, array $data): CourseLecture
    {
        return $course->lectures()->create($data);
    }

    public function update(CourseLecture $lecture, array $data): CourseLecture
    {
        $lecture->update($data);
        return $lecture->fresh();
    }

    public function delete(CourseLecture $lecture): void
    {
        $lecture->delete();
    }
}
