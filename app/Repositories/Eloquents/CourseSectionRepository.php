<?php

namespace App\Repositories\Eloquents;

use App\Models\Course;
use App\Models\CourseSection;
use App\Repositories\Contracts\CourseSectionRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CourseSectionRepository extends BaseRepository implements CourseSectionRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new CourseSection());
    }

    public function allForCourse(Course $course): Collection
    {
        // Eager-pull the enrollment headcount in the same query so the
        // Cohort tab can render "Enrolled / Capacity" without an N+1.
        // `users_courses.group_id` points at `course_sections.id`.
        return $course->sections()
            ->withCount(['enrollments as enrolled_count'])
            ->orderBy('id')
            ->get();
    }

    public function createForCourse(Course $course, array $data): CourseSection
    {
        return $course->sections()->create($data);
    }

    public function syncForCourse(Course $course, array $sections): void
    {
        $submittedIds = collect($sections)->pluck('id')->filter()->all();

        $course->sections()->whereNotIn('id', $submittedIds)->delete();

        foreach ($sections as $section) {
            if (!empty($section['id'])) {
                $course->sections()->where('id', $section['id'])->update(['name' => $section['name']]);
            } else {
                $course->sections()->create(['name' => $section['name']]);
            }
        }
    }
}
