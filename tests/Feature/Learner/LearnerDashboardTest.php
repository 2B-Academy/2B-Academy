<?php

namespace Tests\Feature\Learner;

use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseSection;
use App\Models\UserLectureProgress;
use App\Models\UsersCourse;
use App\Services\CertificatePolicy;
use Tests\Concerns\ConfiguresCertificateRule;
use Tests\Feature\Api\ApiTestCase;

/**
 * GET my/learnings — the composite "My Learnings" dashboard card feed:
 * cohort schedule, delivery type, module completion %, certificate status,
 * all batch-resolved across the learner's course list.
 */
class LearnerDashboardTest extends ApiTestCase
{
    use ConfiguresCertificateRule;

    public function test_learnings_returns_composite_fields_per_course(): void
    {
        ['model' => $user, 'headers' => $headers] = $this->userToken();

        $this->configureCertificateRule(CertificatePolicy::BASIS_SCORE, minScore: 60);

        $course = Course::factory()->create([
            'course_type' => 'blended',
            'certificate' => true,
        ]);
        $cohort = CourseSection::factory()->running()->create([
            'course_id' => $course->id,
            'number_of_sessions' => 6,
        ]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        $lectures = CourseLecture::factory()->count(4)->create(['course_id' => $course->id, 'section_id' => $cohort->id]);
        UserLectureProgress::create(['user_id' => $user->id, 'lecture_id' => $lectures[0]->id, 'progress' => 100, 'completed' => true]);
        UserLectureProgress::create(['user_id' => $user->id, 'lecture_id' => $lectures[1]->id, 'progress' => 100, 'completed' => true]);

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/my/learnings');

        $this->assertSuccess($response);
        $card = collect($response->json('result'))->firstWhere('id', $course->id);

        $this->assertNotNull($card);
        $this->assertSame('blended', $card['delivery_type']);
        $this->assertSame(6, $card['cohort']['session_count']);
        $this->assertNotNull($card['cohort']['start_date']);
        $this->assertSame(50, $card['module_progress_percent']); // 2 of 4 lectures completed
        $this->assertSame('on_track', $card['certificate_status']['status']);
    }

    public function test_learnings_requires_authentication(): void
    {
        $response = $this->getJson(self::BASE . '/my/learnings');
        $this->assertUnauthorized($response);
    }
}
