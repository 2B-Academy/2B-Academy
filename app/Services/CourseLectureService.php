<?php

namespace App\Services;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Repositories\Contracts\CourseLectureRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class CourseLectureService
{
    public function __construct(
        private readonly CourseLectureRepositoryInterface $repo
    ) {}

    public function listForCourse(Course $course): Collection
    {
        return $this->repo->sectionsWithLecturesForCourse($course);
    }

    /**
     * Flat list of every lecture (Module) belonging to a course, ordered by
     * creation. Powers the admin "Content" tab modules list which intentionally
     * presents content as a single numbered list rather than grouped sections.
     *
     * @return \Illuminate\Database\Eloquent\Collection<int, CourseLecture>
     */
    public function listFlatForCourse(Course $course): Collection
    {
        return $course->lectures()->orderBy('id')->get();
    }

    public function create(Course $course, array $data): CourseLecture
    {
        $data = $this->prepare($data, $course);
        return $this->repo->createForCourse($course, $data);
    }

    public function update(CourseLecture $lecture, array $data): CourseLecture
    {
        $data = $this->prepare($data, $lecture->course);
        /** @var CourseLecture */
        return $this->repo->update($lecture, $data);
    }

    public function delete(CourseLecture $lecture): void
    {
        $this->repo->delete($lecture);
    }

    /**
     * Normalize the validated payload before it hits the repository:
     *   - Ensure `section_id` always refers to a real section belonging to the
     *     course — creating a default section when none exists so the admin
     *     "+ Module" flow never has to ask the user about sections.
     *   - Derive `type` (url|file) from `content_type` when not explicit.
     */
    private function prepare(array $data, ?Course $course): array
    {
        if ($course) {
            $section = $this->resolveSection($course, $data['section_id'] ?? null);
            $data['section_id'] = $section->id;
        }

        $contentType = $data['content_type'] ?? 'video';
        if (empty($data['type'])) {
            $data['type'] = $contentType === 'document' ? 'file' : 'url';
        }

        if (($data['learner_scope'] ?? 'all') !== 'cohort') {
            $data['session_id'] = null;
        }

        return $data;
    }

    private function resolveSection(Course $course, ?int $sectionId): CourseSection
    {
        if ($sectionId) {
            $section = $course->sections()->whereKey($sectionId)->first();
            if ($section) {
                return $section;
            }
        }

        $section = $course->sections()->orderBy('id')->first();
        if ($section) {
            return $section;
        }

        // No sections yet — provision a single hidden "Default" section that
        // groups every lecture for this course. Title is bilingual so it
        // satisfies the translatable contract.
        return $course->sections()->create([
            'name' => ['en' => 'Default', 'ar' => 'الافتراضي'],
        ]);
    }
}
