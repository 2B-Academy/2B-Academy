<?php

namespace Tests\Feature\Learner;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Tests\Feature\Api\ApiTestCase;

/**
 * Lecture content-type + completion-signal exposure for the course-player:
 *   - my-progress now surfaces content_type / require_completion / module
 *     (the course_lectures.section_id -> course_sections.name grouping that
 *     drives the sidebar's "Week 1: ..." headings).
 *   - POST .../progress accepts an explicit `confirmed` boolean serving all
 *     four content types (video/document/article "Did you complete this?"
 *     Yes/No, and link's "Mark as complete"), on top of the legacy numeric
 *     `progress` >= 90 rule which keeps working unchanged.
 */
class LectureProgressSignalTest extends ApiTestCase
{
    public function test_my_progress_exposes_content_type_require_completion_and_module_grouping(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        // NOTE: pass `name` as an array, not a pre-json_encode()'d string —
        // HasTranslations::setAttribute() treats a scalar string as "the
        // translation for the CURRENT app locale" and wraps it, which would
        // double-encode an already-JSON string (a pre-existing quirk shared
        // factories like CourseSectionFactory's default trip over too, just
        // never asserted on before now).
        $section = CourseSection::factory()->create([
            'course_id' => $course->id,
            'name' => ['en' => 'Week 1: CX Foundations', 'ar' => 'الأسبوع 1'],
        ]);
        $lecture = CourseLecture::factory()->create([
            'course_id' => $course->id,
            'section_id' => $section->id,
            'content_type' => 'article',
            'require_completion' => true,
        ]);

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/courses/{$course->id}/my-progress");

        $this->assertSuccess($response);
        $row = collect($response->json('result.lectures'))->firstWhere('lecture_id', $lecture->id);

        $this->assertSame('article', $row['content_type']);
        $this->assertTrue($row['require_completion']);
        $this->assertSame($section->id, $row['module']['id']);
        $this->assertSame('Week 1: CX Foundations', $row['module']['name']);
    }

    public function test_confirmed_true_marks_complete_regardless_of_progress(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $lecture = CourseLecture::factory()->create(['course_id' => $course->id, 'content_type' => 'link']);

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}/progress",
            ['confirmed' => true],
        );

        $this->assertSuccess($response);
        $this->assertTrue($response->json('result.completed'));
        $this->assertSame(100, $response->json('result.progress'));
    }

    public function test_confirmed_false_leaves_lecture_incomplete(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $lecture = CourseLecture::factory()->create(['course_id' => $course->id, 'content_type' => 'video']);

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}/progress",
            ['confirmed' => false],
        );

        $this->assertSuccess($response);
        $this->assertFalse($response->json('result.completed'));
    }

    public function test_legacy_numeric_progress_still_works_without_confirmed(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $lecture = CourseLecture::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}/progress",
            ['progress' => 95],
        );

        $this->assertSuccess($response);
        $this->assertTrue($response->json('result.completed'));
        $this->assertSame(95, $response->json('result.progress'));
    }

    public function test_progress_is_still_required_when_confirmed_is_absent(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $lecture = CourseLecture::factory()->create(['course_id' => $course->id]);

        $response = $this->withHeaders($headers)->postJson(
            self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}/progress",
            [],
        );

        $this->assertValidationError($response);
    }
}
