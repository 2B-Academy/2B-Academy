<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseSection;
use App\Repositories\Contracts\CourseSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseSectionService
{
    public function __construct(
        private readonly CourseSectionRepositoryInterface $repo
    ) {}

    public function listForCourse(Course $course): Collection
    {
        return $this->repo->allForCourse($course);
    }

    public function sync(Course $course, array $sections): Collection
    {
        $this->repo->syncForCourse($course, $sections);
        return $this->repo->allForCourse($course);
    }

    public function create(Course $course, array $data): CourseSection
    {
        return $course->sections()->create(['name' => $data['name']]);
    }

    public function update(CourseSection $section, array $data): CourseSection
    {
        $section->update(['name' => $data['name']]);
        return $section->fresh();
    }

    public function delete(CourseSection $section): void
    {
        $section->delete();
    }
}
