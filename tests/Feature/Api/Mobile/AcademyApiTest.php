<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Category;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\UsersCourse;

/**
 * S-01 → S-04: Academy entry, list, detail, enrolment.
 */
class AcademyApiTest extends MobileTestCase
{
    /** A course that is joinable for a brand-new learner. */
    private function joinableCourse(): Course
    {
        $course = Course::factory()->create();
        CourseSection::factory()->create(['course_id' => $course->id]);

        return $course;
    }

    // ── S-01 summary ────────────────────────────────────────────────

    public function test_summary_reports_available_courses(): void
    {
        $user = $this->employee();
        $this->joinableCourse();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/summary');

        $this->assertSuccess($response);
        $response->assertJsonPath('result.available_count', 1)
                 ->assertJsonPath('result.has_available', true);
    }

    // ── S-02 list ───────────────────────────────────────────────────

    public function test_courses_list_is_paginated_and_shows_joinable_course(): void
    {
        $user   = $this->employee();
        $course = $this->joinableCourse();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonStructure(['result' => [[
                     'id', 'title', 'course_type', 'hours', 'has_certificate',
                     'rating' => ['avg', 'count'], 'next_cohort',
                 ]]]);
        $this->assertSame($course->id, $response->json('result.0.id'));
    }

    public function test_courses_list_filters_by_category(): void
    {
        $user = $this->employee();

        $catA = Category::factory()->create();
        $catB = Category::factory()->create();
        $courseA = Course::factory()->create(['category_id' => $catA->id]);
        $courseB = Course::factory()->create(['category_id' => $catB->id]);
        CourseSection::factory()->create(['course_id' => $courseA->id]);
        CourseSection::factory()->create(['course_id' => $courseB->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses?category_id=' . $catA->id);

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1);
        $this->assertSame($courseA->id, $response->json('result.0.id'));
    }

    public function test_already_enrolled_course_is_not_listed_as_available(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 0);
    }

    // ── S-03 detail ─────────────────────────────────────────────────

    public function test_course_detail_returns_cta_enrol_now_for_joinable_course(): void
    {
        $user   = $this->employee();
        $course = $this->joinableCourse();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses/' . $course->id);

        $this->assertSuccess($response);
        $response->assertJsonPath('result.id', $course->id)
                 ->assertJsonPath('result.cta.state', 'enrol_now')
                 ->assertJsonStructure(['result' => [
                     'id', 'title', 'description', 'allow_attendance',
                     'units', 'cohorts', 'anchor_cohort', 'cta' => ['state', 'label_key', 'enabled'],
                 ]]);
    }

    public function test_course_detail_reflects_allow_attendances_flag(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create(['allow_attendances' => true]);
        CourseSection::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses/' . $course->id);

        $this->assertSuccess($response);
        // Regression: the resource used to read the wrong column and always returned false.
        $response->assertJsonPath('result.allow_attendance', true);
    }

    public function test_course_detail_404_for_missing_course(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/academy/courses/999999');

        $this->assertError($response, 404);
    }

    // ── S-03 → S-04 enrol ───────────────────────────────────────────

    public function test_enrol_succeeds_into_open_cohort(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/enrol', [
                             'cohort_id' => $cohort->id,
                         ]);

        $this->assertSuccess($response, 201);
        $this->assertDatabaseHas('users_courses', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);
    }

    public function test_enrol_when_already_enrolled_returns_200_already(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/enrol', [
                             'cohort_id' => $cohort->id,
                         ]);

        $this->assertSuccess($response, 200);
        // Still exactly one enrolment row.
        $this->assertSame(1, UsersCourse::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }

    public function test_enrol_into_full_cohort_returns_409(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->full()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/enrol', [
                             'cohort_id' => $cohort->id,
                         ]);

        $this->assertError($response, 409);
    }

    public function test_enrol_into_closed_cohort_returns_409(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->closed()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/enrol', [
                             'cohort_id' => $cohort->id,
                         ]);

        $this->assertError($response, 409);
    }

    public function test_enrol_with_invalid_cohort_id_returns_422(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        CourseSection::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/academy/courses/' . $course->id . '/enrol', [
                             'cohort_id' => 999999,
                         ]);

        $this->assertError($response, 422);
    }
}
