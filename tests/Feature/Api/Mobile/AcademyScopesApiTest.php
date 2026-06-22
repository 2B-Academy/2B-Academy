<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\JobTitle;
use App\Models\QualificationSkill;
use App\Models\User;

/**
 * S-02 scope chips (All / Special / General).
 *
 * Business rule (confirmed): Special and General are a TRUE PARTITION of
 * the available set, so `all = special + general` must always hold.
 *   • Special = courses tied to the employee's job-title qualifications.
 *   • General = every other available course (the complement).
 */
class AcademyScopesApiTest extends MobileTestCase
{
    /** A joinable course, optionally tagged with a qualification skill. */
    private function joinableCourse(?int $skillId = null): Course
    {
        $course = Course::factory()->create();
        CourseSection::factory()->create(['course_id' => $course->id]);
        if ($skillId !== null) {
            $course->qualificationSkills()->attach($skillId);
        }

        return $course;
    }

    /** Employee whose job title requires the given skill. */
    private function employeeWithSkill(QualificationSkill $skill): User
    {
        $jobTitle = JobTitle::factory()->create();
        $jobTitle->qualificationSkills()->attach($skill->id);

        return $this->employee(['job_title_id' => $jobTitle->id]);
    }

    private function scopeCounts(User $user): array
    {
        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/scopes');
        $this->assertSuccess($response);

        return collect($response->json('result'))->keyBy('key')->map->count->all();
    }

    public function test_scopes_partition_reconciles_special_plus_general_equals_all(): void
    {
        $skill = QualificationSkill::factory()->create();
        $user  = $this->employeeWithSkill($skill);

        $this->joinableCourse($skill->id); // special (role-matched)
        $this->joinableCourse();           // general (no skill)

        $counts = $this->scopeCounts($user);

        $this->assertSame(2, $counts['all']);
        $this->assertSame(1, $counts['special']);
        $this->assertSame(1, $counts['general']);
        $this->assertSame($counts['all'], $counts['special'] + $counts['general']);
    }

    public function test_private_unmatched_course_counts_as_general_not_orphaned(): void
    {
        // Regression: a private (for_public=0) course not tied to the
        // employee's skills used to land in neither chip → all:1/0/0.
        $skill = QualificationSkill::factory()->create();
        $user  = $this->employeeWithSkill($skill);

        $course = Course::factory()->create(['for_public' => false]);
        CourseSection::factory()->create(['course_id' => $course->id]);

        $counts = $this->scopeCounts($user);

        $this->assertSame(1, $counts['all']);
        $this->assertSame(0, $counts['special']);
        $this->assertSame(1, $counts['general']);
    }

    public function test_employee_without_job_title_sees_everything_as_general(): void
    {
        $user = $this->employee(['job_title_id' => null]);
        $this->joinableCourse();
        $this->joinableCourse();

        $counts = $this->scopeCounts($user);

        $this->assertSame(2, $counts['all']);
        $this->assertSame(0, $counts['special']);
        $this->assertSame(2, $counts['general']);
    }

    public function test_course_list_filters_by_scope(): void
    {
        $skill = QualificationSkill::factory()->create();
        $user  = $this->employeeWithSkill($skill);

        $special = $this->joinableCourse($skill->id);
        $general = $this->joinableCourse();

        $specialList = $this->withHeaders($this->headersFor($user))
                            ->getJson(self::BASE . '/mobile/academy/courses?scope=special');
        $this->assertPaginated($specialList);
        $specialList->assertJsonPath('meta.total', 1)
                    ->assertJsonPath('result.0.id', $special->id);

        $generalList = $this->withHeaders($this->headersFor($user))
                            ->getJson(self::BASE . '/mobile/academy/courses?scope=general');
        $this->assertPaginated($generalList);
        $generalList->assertJsonPath('meta.total', 1)
                    ->assertJsonPath('result.0.id', $general->id);
    }
}
