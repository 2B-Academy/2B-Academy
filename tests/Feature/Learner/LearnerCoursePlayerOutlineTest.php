<?php

namespace Tests\Feature\Learner;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseExamQuestion;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use Tests\Feature\Api\ApiTestCase;

/**
 * Course-player sidebar composite: lectures grouped by their section's
 * "Week N" label, a rich quiz placed in its own section's group, module
 * progress totals, and the certificate-status projection.
 */
class LearnerCoursePlayerOutlineTest extends ApiTestCase
{
    public function test_outline_groups_lectures_by_section_and_includes_a_rich_quiz(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create(['certificate' => false]);

        $week1 = CourseSection::factory()->create([
            'course_id' => $course->id,
            'name' => ['en' => 'Week 1: Foundations', 'ar' => 'الأسبوع 1'],
        ]);
        $week2 = CourseSection::factory()->create([
            'course_id' => $course->id,
            'name' => ['en' => 'Week 2: Practice', 'ar' => 'الأسبوع 2'],
        ]);

        $lecture1 = CourseLecture::factory()->create([
            'course_id' => $course->id,
            'section_id' => $week1->id,
            'content_type' => 'video',
        ]);
        $lecture2 = CourseLecture::factory()->create([
            'course_id' => $course->id,
            'section_id' => $week2->id,
            'content_type' => 'article',
        ]);

        $quiz = CourseExam::factory()->create([
            'course_id' => $course->id,
            'section_id' => $week2->id,
            'title' => ['en' => 'Week 2 Quiz', 'ar' => 'اختبار الأسبوع 2'],
        ]);
        CourseExamQuestion::create([
            'course_exam_id' => $quiz->id,
            'position' => 0,
            'type' => 'mcq',
            'score' => 10,
            'question' => json_encode(['en' => 'x', 'ar' => 'x']),
            'question_en' => 'A rich question?',
            'options_en' => ['A', 'B'],
            'correct_answer_en' => 'A',
        ]);

        $response = $this->withHeaders($headers)->getJson(self::BASE . "/my/courses/{$course->id}/outline");

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertSame($course->id, $result['course_id']);
        $this->assertSame(2, $result['modules_total']);
        $this->assertSame(0, $result['modules_completed']);
        $this->assertArrayHasKey('certificate_status', $result);
        // certificate=false on this course -> no certificate offered.
        $this->assertNull($result['certificate_status']['status']);

        $weeksByLabel = collect($result['weeks'])->keyBy('label');

        $this->assertTrue($weeksByLabel->has('Week 1: Foundations'));
        $this->assertTrue($weeksByLabel->has('Week 2: Practice'));

        $week1Items = collect($weeksByLabel->get('Week 1: Foundations')['items']);
        $this->assertTrue($week1Items->contains(fn ($i) => $i['kind'] === 'lecture' && $i['id'] === $lecture1->id));

        $week2Items = collect($weeksByLabel->get('Week 2: Practice')['items']);
        $this->assertTrue($week2Items->contains(fn ($i) => $i['kind'] === 'lecture' && $i['id'] === $lecture2->id));
        $this->assertTrue($week2Items->contains(fn ($i) => $i['kind'] === 'quiz' && $i['id'] === $quiz->id && $i['completed'] === false));

        // Exactly one item across the whole playlist is flagged active (the first incomplete one).
        $activeCount = collect($result['weeks'])
            ->flatMap(fn ($w) => $w['items'])
            ->filter(fn ($i) => $i['active'])
            ->count();
        $this->assertSame(1, $activeCount);
    }

    public function test_single_lecture_endpoint_returns_full_article_body(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $lecture = CourseLecture::factory()->create([
            'course_id' => $course->id,
            'content_type' => 'article',
            'content' => 'Full article body text.',
            'require_completion' => true,
        ]);

        $response = $this->withHeaders($headers)
            ->getJson(self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}");

        $this->assertSuccess($response);
        $result = $response->json('result');

        $this->assertSame($lecture->id, $result['id']);
        $this->assertSame('article', $result['content_type']);
        $this->assertSame('Full article body text.', $result['body']);
        $this->assertNull($result['content_url']);
        $this->assertTrue($result['require_completion']);
        $this->assertFalse($result['completed']);
    }

    public function test_single_lecture_endpoint_404s_when_lecture_belongs_to_a_different_course(): void
    {
        ['headers' => $headers] = $this->userToken();

        $course = Course::factory()->create();
        $otherCourse = Course::factory()->create();
        $lecture = CourseLecture::factory()->create(['course_id' => $otherCourse->id]);

        $response = $this->withHeaders($headers)
            ->getJson(self::BASE . "/courses/{$course->id}/lectures/{$lecture->id}");

        $response->assertStatus(404);
    }
}
