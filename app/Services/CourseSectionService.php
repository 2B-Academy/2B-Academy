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
        $payload = $this->fillable($data);

        // A new cohort inherits the course's planned session count unless
        // the admin overrode it in the dialog (Figma 332:10708).
        if (! array_key_exists('number_of_sessions', $payload) || $payload['number_of_sessions'] === null) {
            $payload['number_of_sessions'] = $course->number_of_sessions;
        }

        return $this->repo->createForCourse($course, $payload);
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
        foreach (['start_date', 'end_date', 'capacity', 'status', 'number_of_sessions', 'avg_session_time'] as $key) {
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
