<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\JobTitle;
use App\Models\QualificationSkill;

/**
 * Learner-web Catalogue additions on top of the shared Academy list/detail
 * endpoints: level, duration bucket, job role, type filters; sort; the
 * Type/Level/Duration facet counts meta block; per-card cta; instructor
 * bio; overview bullets; and the "Notify me" intent endpoint.
 */
class AcademyCatalogueFiltersTest extends MobileTestCase
{
    /** A joinable course with the given level/course_type and a default (2-4 week) cohort. */
    private function courseWith(array $attributes = []): Course
    {
        $course = Course::factory()->create($attributes);
        CourseSection::factory()->create(['course_id' => $course->id]);

        return $course;
    }

    // ── level filter + field ────────────────────────────────────────

    public function test_courses_list_filters_by_level_and_exposes_level_field(): void
    {
        $user = $this->employee();

        $beginner     = $this->courseWith(['level' => 'beginner']);
        $professional = $this->courseWith(['level' => 'professional']);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?level=beginner');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('result.0.id', $beginner->id)
                 ->assertJsonPath('result.0.level', 'beginner');
    }

    public function test_courses_list_rejects_unknown_level(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?level=not_a_level');

        $this->assertError($response, 422);
    }

    // ── type filter ─────────────────────────────────────────────────

    public function test_courses_list_filters_by_type(): void
    {
        $user = $this->employee();

        $online  = $this->courseWith(['course_type' => 'online']);
        $offline = $this->courseWith(['course_type' => 'offline']);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?type=offline');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('result.0.id', $offline->id);
    }

    // ── duration bucket filter + field ──────────────────────────────

    public function test_courses_list_exposes_and_filters_by_duration_weeks(): void
    {
        $user = $this->employee();

        // Default factory cohort: start +20d, end +40d => 21-day span => ceil(21/7) = 3 weeks => "2_4_weeks".
        $shortCourse = $this->courseWith();

        // A long-span cohort => > 8 weeks.
        $longCourse = Course::factory()->create();
        CourseSection::factory()->create([
            'course_id'  => $longCourse->id,
            'start_date' => now()->addDays(5)->toDateString(),
            'end_date'   => now()->addDays(75)->toDateString(),
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?duration=2_4_weeks');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('result.0.id', $shortCourse->id)
                 ->assertJsonPath('result.0.duration_weeks', 3);

        $responseLong = $this->withHeaders($this->headersFor($user))
                             ->getJson(self::BASE . '/mobile/academy/courses?duration=8_plus_weeks');

        $responseLong->assertJsonPath('meta.total', 1)
                     ->assertJsonPath('result.0.id', $longCourse->id);
    }

    // ── job role filter (no counts) ─────────────────────────────────

    public function test_courses_list_filters_by_job_role_via_qualification_skill_join(): void
    {
        $user = $this->employee();

        $skill      = QualificationSkill::factory()->create();
        $jobRole    = JobTitle::factory()->create();
        $jobRole->qualificationSkills()->attach($skill->id);

        $matching    = $this->courseWith();
        $matching->qualificationSkills()->attach($skill->id);
        $nonMatching = $this->courseWith();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?job_role_id=' . $jobRole->id);

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('result.0.id', $matching->id);
    }

    // ── facet counts (Type / Level / Duration) ──────────────────────

    public function test_courses_list_meta_includes_type_level_duration_facet_counts(): void
    {
        $user = $this->employee();

        $this->courseWith(['level' => 'beginner', 'course_type' => 'online']);
        $this->courseWith(['level' => 'professional', 'course_type' => 'offline']);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses');

        $this->assertPaginated($response);
        $response->assertJsonStructure([
            'meta' => ['filters' => ['type', 'level', 'duration']],
        ]);
        $response->assertJsonPath('meta.filters.level.beginner', 1)
                 ->assertJsonPath('meta.filters.level.professional', 1)
                 ->assertJsonPath('meta.filters.type.online', 1)
                 ->assertJsonPath('meta.filters.type.offline', 1);
    }

    public function test_facet_counts_reflect_other_active_filters(): void
    {
        $user = $this->employee();

        // Two "online" courses split across levels; one "offline" course.
        $this->courseWith(['level' => 'beginner', 'course_type' => 'online']);
        $this->courseWith(['level' => 'professional', 'course_type' => 'online']);
        $this->courseWith(['level' => 'beginner', 'course_type' => 'offline']);

        // Filtering by type=online should narrow the LEVEL facet counts to
        // only the two online courses (1 beginner, 1 professional) while
        // the TYPE facet itself still reflects every OTHER filter (none
        // here), so online=2 / offline=1.
        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?type=online');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.filters.level.beginner', 1)
                 ->assertJsonPath('meta.filters.level.professional', 1)
                 ->assertJsonPath('meta.filters.type.online', 2)
                 ->assertJsonPath('meta.filters.type.offline', 1);
    }

    // ── sort ─────────────────────────────────────────────────────────

    public function test_courses_list_sorts_by_newest(): void
    {
        $user = $this->employee();

        $older = $this->courseWith();
        $older->forceFill(['created_at' => now()->subDays(5)])->save();
        $newer = $this->courseWith();
        $newer->forceFill(['created_at' => now()->subDay()])->save();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?sort=newest');

        $this->assertPaginated($response);
        $response->assertJsonPath('result.0.id', $newer->id)
                 ->assertJsonPath('result.1.id', $older->id);
    }

    public function test_courses_list_rejects_unknown_sort(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?sort=random');

        $this->assertError($response, 422);
    }

    // ── per-card cta ─────────────────────────────────────────────────

    public function test_courses_list_card_exposes_cta_state(): void
    {
        $user   = $this->employee();
        $course = $this->courseWith();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses');

        $this->assertPaginated($response);
        $response->assertJsonPath('result.0.id', $course->id)
                 ->assertJsonPath('result.0.cta.state', 'enrol_now')
                 ->assertJsonStructure(['result' => [[
                     'cta' => ['state', 'label_key', 'enabled'],
                 ]]]);
    }

    // ── instructor bio + overview bullets (detail) ──────────────────

    public function test_course_detail_exposes_instructor_bio_and_overview_bullets(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create([
            'what_students_will_learn' => ['en' => ['Learn A', 'Learn B'], 'ar' => ['تعلم أ']],
            'requirements'              => ['en' => ['Req A'], 'ar' => []],
        ]);
        CourseSection::factory()->create(['course_id' => $course->id]);

        // No InstructorFactory exists in this codebase yet — create directly.
        $instructor = \App\Models\Instructor::create([
            'name'      => ['en' => 'Jane Doe', 'ar' => 'جين دو'],
            'email'     => 'jane.doe.' . uniqid() . '@example.test',
            'image'     => 'instructors/jane.png',
            'job_title' => 'Senior Trainer',
            'bio'       => ['en' => 'Seasoned trainer.', 'ar' => 'مدرب متمرس.'],
        ]);
        $course->instructors()->attach($instructor->id);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses/' . $course->id);

        $this->assertSuccess($response);
        $response->assertJsonPath('result.instructors.0.bio', 'Seasoned trainer.')
                 ->assertJsonPath('result.what_students_will_learn.en', ['Learn A', 'Learn B'])
                 ->assertJsonPath('result.requirements.en', ['Req A'])
                 ->assertJsonPath('result.level', $course->level)
                 ->assertJsonPath('result.duration_weeks', 3);
    }

    // ── notify-me (GAP 4) ────────────────────────────────────────────
    //
    // This endpoint is only routed on the learner-web surface
    // (routes/apis/learner.php — Sanctum `auth.user`+`role:User`), NOT
    // on the S2S mobile surface, so these hit `/learner/academy/...`
    // with a real Sanctum bearer token instead of the mobile headers
    // the rest of this file uses.

    private function learnerToken(): array
    {
        $user  = \App\Models\User::factory()->create();
        $token = $user->createToken('test')->plainTextToken;

        return ['user' => $user, 'headers' => ['Authorization' => 'Bearer ' . $token]];
    }

    public function test_notify_me_is_idempotent(): void
    {
        ['user' => $user, 'headers' => $headers] = $this->learnerToken();
        $course = $this->courseWith();

        $first = $this->withHeaders($headers)
                      ->postJson(self::BASE . '/learner/academy/courses/' . $course->id . '/notify-me');
        $this->assertSuccess($first);

        $second = $this->withHeaders($headers)
                       ->postJson(self::BASE . '/learner/academy/courses/' . $course->id . '/notify-me');
        $this->assertSuccess($second);

        $this->assertSame(1, \App\Models\CourseNotifyInterest::query()
            ->where('user_id', $user->id)
            ->where('course_id', $course->id)
            ->count());
    }

    public function test_notify_me_404_for_missing_course(): void
    {
        ['headers' => $headers] = $this->learnerToken();

        $response = $this->withHeaders($headers)
                         ->postJson(self::BASE . '/learner/academy/courses/999999/notify-me');

        $this->assertError($response, 404);
    }

    public function test_notify_me_is_not_routed_on_the_mobile_surface(): void
    {
        $user   = $this->employee();
        $course = $this->courseWith();

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/notify-me');

        $this->assertError($response, 404);
    }
}
