<?php

namespace Tests\Feature\Certificates;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserExam;
use App\Models\UsersCourse;
use App\Services\CertificatePolicy;
use App\Services\CertificateProjectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ConfiguresCertificateRule;
use Tests\TestCase;

/**
 * Certificate status projection (On track / At risk / Blocked) — the
 * business logic behind the course-player's persistent certificate badge.
 *
 * The rule comes from Platform Config via App\Services\CertificatePolicy, so
 * each scenario configures it through {@see ConfiguresCertificateRule} rather
 * than through per-course columns.
 */
class CertificateProjectionTest extends TestCase
{
    use RefreshDatabase;
    use ConfiguresCertificateRule;

    private function service(): CertificateProjectionService
    {
        return app(CertificateProjectionService::class);
    }

    private function enrollInCohort(User $user, Course $course, CourseSection $cohort): void
    {
        UsersCourse::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'group_id' => $cohort->id,
        ]);
    }

    public function test_status_is_earned_when_a_certificate_already_exists(): void
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_SCORE, minScore: 60);
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true]);
        UserCertificate::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('earned', $result['status']);
        $this->assertNull($result['blocked_reason']);
    }

    public function test_on_track_when_exam_not_yet_attempted_and_cohort_still_running(): void
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_SCORE, minScore: 60);
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true]);
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        $this->enrollInCohort($user, $course, $cohort);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('on_track', $result['status']);
        $this->assertNull($result['score_percent']);
    }

    public function test_at_risk_when_score_is_below_threshold_but_cohort_still_running(): void
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_SCORE, minScore: 60);
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true]);
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        $this->enrollInCohort($user, $course, $cohort);

        $exam = CourseExam::factory()->final()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'degree' => 100]);
        UserExam::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'exam_id' => $exam->id,
            'user_degree' => 30, // 30% < 60% threshold
            'status' => 'fail',
        ]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('at_risk', $result['status']);
        $this->assertNull($result['blocked_reason']);
        $this->assertSame(30, $result['score_percent']);
    }

    public function test_blocked_with_score_reason_when_cohort_has_ended_below_threshold(): void
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_SCORE, minScore: 60);
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true]);
        $cohort = CourseSection::factory()->create([
            'course_id' => $course->id,
            'start_date' => now()->subDays(60)->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(),
        ]);
        $this->enrollInCohort($user, $course, $cohort);

        $exam = CourseExam::factory()->final()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'degree' => 100]);
        UserExam::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'exam_id' => $exam->id,
            'user_degree' => 30,
            'status' => 'fail',
        ]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('blocked', $result['status']);
        $this->assertSame('score', $result['blocked_reason']);
        $this->assertSame(
            __('messages.certificate_status.blocked_score'),
            $result['message'],
        );
    }

    public function test_blocked_with_both_reason_when_attendance_and_score_both_fail(): void
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_BOTH, minAttendance: 75, minScore: 60);
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'certificate' => true,
            // A planned session count is what makes attendance measurable at
            // all — without it the attendance requirement would be skipped.
            'number_of_sessions' => 10,
        ]);
        // Cohort deliberately has no `number_of_sessions` target of its own so
        // completion falls back to the (already-passed) end_date rule —
        // Course::deriveCohortStatus only completes via session-count once
        // real CourseSession rows exist, which this scenario doesn't need.
        $cohort = CourseSection::factory()->create([
            'course_id' => $course->id,
            'start_date' => now()->subDays(60)->toDateString(),
            'end_date' => now()->subDays(1)->toDateString(),
        ]);
        $this->enrollInCohort($user, $course, $cohort);

        // No attendance rows at all => 0%.
        $exam = CourseExam::factory()->final()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'degree' => 100]);
        UserExam::factory()->create([
            'user_id' => $user->id,
            'course_id' => $course->id,
            'exam_id' => $exam->id,
            'user_degree' => 10,
            'status' => 'fail',
        ]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('blocked', $result['status']);
        $this->assertSame('both', $result['blocked_reason']);
        $this->assertSame(
            __('messages.certificate_status.blocked_both'),
            $result['message'],
        );
    }

    public function test_no_projection_when_course_does_not_offer_a_certificate(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => false]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertNull($result['status']);
    }
}
