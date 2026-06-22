<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\CourseSection;
use App\Repositories\Contracts\CourseRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class CourseService
{
    use HasFile;

    public function __construct(
        private readonly CourseRepositoryInterface $courseRepository,
    ) {}

    public function list(
        int     $perPage    = 15,
        ?string $search     = null,
        ?int    $categoryId = null,
        ?bool   $active     = null,
        ?string $courseType = null,
        ?string $status     = null,
    ): LengthAwarePaginator {
        return $this->courseRepository->paginateWithFilters(
            $perPage, $search, $categoryId, $active, $courseType, $status,
        );
    }

    /**
     * @return array{all: int, active: int, inactive: int, pending: int, upcoming: int}
     */
    public function tabCounts(): array
    {
        return $this->courseRepository->tabCounts();
    }

    public function allActive(): Collection
    {
        return $this->courseRepository->allActive();
    }

    public function activePluckedTitles(): Collection
    {
        return $this->courseRepository->activePluckedTitles();
    }

    public function findOrFail(int $id): Course
    {
        return $this->courseRepository->findWithRelations($id);
    }

    public function create(array $data, ?UploadedFile $image = null): Course
    {
        if ($image) {
            $data['image'] = $this->uploadRequestFile('Course', request(), 'image');
        } else {
            $data['image'] = null;
        }
        $data['active']            = (bool) ($data['active']            ?? false);
        $data['outside_materials'] = (bool) ($data['outside_materials'] ?? false);
        $data['is_evaluate']       = (bool) ($data['is_evaluate']       ?? false);
        $data['allow_attendances'] = (bool) ($data['allow_attendances'] ?? false);

        $instructors           = $data['instructors']             ?? [];
        $qualificationSkillIds = $data['qualification_skill_ids'] ?? [];
        // Pop the cohort window off the payload so it doesn't trip the
        // Course model's guarded write — it goes onto a CourseSection
        // instead (see syncFirstCohort below).
        $cohortStart = $data['cohort_start'] ?? null;
        $cohortEnd   = $data['cohort_end']   ?? null;
        unset(
            $data['instructors'], $data['qualification_skill_ids'],
            $data['cohort_start'], $data['cohort_end'],
        );

        $course = $this->courseRepository->create($data);

        if ($instructors) {
            $course->instructors()->attach($instructors);
        }

        if ($qualificationSkillIds) {
            $course->qualificationSkills()->sync(array_values(array_unique($qualificationSkillIds)));
        }

        if ($cohortStart || $cohortEnd) {
            $this->syncFirstCohort($course, $cohortStart, $cohortEnd);
        }

        return $this->courseRepository->findWithBasicRelations($course->id);
    }

    public function update(Course $course, array $data, ?UploadedFile $image = null): Course
    {
        if ($image) {
            $data['image'] = $this->uploadRequestFile('Course', request(), 'image');
        }
        $data['active']            = (bool) ($data['active']            ?? false);
        $data['outside_materials'] = (bool) ($data['outside_materials'] ?? false);
        $data['is_evaluate']       = (bool) ($data['is_evaluate']       ?? false);
        $data['allow_attendances'] = (bool) ($data['allow_attendances'] ?? false);

        $instructors           = $data['instructors']             ?? null;
        $hasSkillsPayload      = array_key_exists('qualification_skill_ids', $data);
        $qualificationSkillIds = $data['qualification_skill_ids'] ?? null;
        $hasCohortStart        = array_key_exists('cohort_start', $data);
        $hasCohortEnd          = array_key_exists('cohort_end', $data);
        $cohortStart           = $data['cohort_start'] ?? null;
        $cohortEnd             = $data['cohort_end']   ?? null;
        unset(
            $data['instructors'], $data['qualification_skill_ids'],
            $data['cohort_start'], $data['cohort_end'],
        );

        $course = $this->courseRepository->update($course, $data);

        if (!is_null($instructors)) {
            $course->instructors()->sync($instructors);
        }

        if ($hasSkillsPayload) {
            $course->qualificationSkills()->sync(
                array_values(array_unique((array) ($qualificationSkillIds ?? []))),
            );
        }

        if ($hasCohortStart || $hasCohortEnd) {
            $this->syncFirstCohort($course, $cohortStart, $cohortEnd);
        }

        return $this->courseRepository->findWithBasicRelations($course->id);
    }

    /**
     * Upsert the course's *first* cohort from the inline cohort window
     * captured on the Add / Edit Course dialogs. We treat the earliest
     * (lowest id) `course_sections` row as the canonical first cohort —
     * matching what `CourseDetailResource` surfaces for the form pre-fill.
     *
     * The cohort `status` is intentionally left to the calendar — once
     * the row exists with a date window, `Course::deriveCohortStatus`
     * + the daily `cohorts:sync-statuses` command keep it in sync.
     */
    private function syncFirstCohort(Course $course, ?string $start, ?string $end): void
    {
        $section = $course->sections()->orderBy('id')->first();

        $payload = [
            'start_date' => $start ? Carbon::parse($start)->toDateString() : null,
            'end_date'   => $end   ? Carbon::parse($end)->toDateString()   : null,
        ];

        if ($section === null) {
            // Seed the very first cohort with a sensible default name
            // ("Cohort 1") so the table on the Course Detail page has
            // something to render before the admin renames it.
            CourseSection::create(array_merge($payload, [
                'course_id' => $course->id,
                'name'      => ['en' => 'Cohort 1', 'ar' => 'الدفعة 1'],
                'status'    => 'scheduled',
            ]));
            return;
        }

        $section->fill($payload)->save();
    }

    public function delete(Course $course): bool
    {
        return $this->courseRepository->delete($course);
    }
}
