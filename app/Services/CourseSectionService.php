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
        return $this->repo->createForCourse($course, $this->fillable($data));
    }

    public function update(CourseSection $section, array $data): CourseSection
    {
        /** @var CourseSection */
        return $this->repo->update($section, $this->fillable($data));
    }

    /**
     * Project the validated request payload down to the columns the
     * `course_sections` table actually has. The keys that are missing
     * from `$data` are skipped so partial PATCH-style updates work too.
     *
     * Keeping this in the service (not the repository) means the request
     * shape and the persistence shape stay decoupled — the repo just gets
     * a clean array of column => value pairs.
     */
    private function fillable(array $data): array
    {
        $out = ['name' => $data['name']];
        foreach (['start_date', 'end_date', 'capacity', 'status', 'avg_session_time'] as $key) {
            if (array_key_exists($key, $data)) {
                $out[$key] = $data[$key];
            }
        }
        return $out;
    }

    public function delete(CourseSection $section): void
    {
        $this->repo->delete($section);
    }
}
