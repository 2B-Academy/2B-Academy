<?php

namespace Tests\Feature\Certificates;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserExam;
use App\Models\UsersCourse;
use App\Services\CertificateProjectionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Certificate status projection (On track / At risk / Blocked) — the
 * business logic behind the course-player's persistent certificate badge.
 * See CertificateProjectionService for the config this reads
 * (courses.certificate_mode / certificate_attendance_threshold /
 * certificate_score_threshold — new, confirmed-absent-before config).
 */
class CertificateProjectionTest extends TestCase
{
    use RefreshDatabase;

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
        $user = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true]);
        UserCertificate::factory()->create(['user_id' => $user->id, 'course_id' => $course->id]);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('earned', $result['status']);
        $this->assertNull($result['blocked_reason']);
    }

    public function test_on_track_when_exam_not_yet_attempted_and_cohort_still_running(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'certificate' => true,
            'certificate_mode' => Course::CERTIFICATE_MODE_SCORE,
        ]);
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        $this->enrollInCohort($user, $course, $cohort);

        $result = $this->service()->projectForCourse($user, $course);

        $this->assertSame('on_track', $result['status']);
        $this->assertNull($result['score_percent']);
    }

    public function test_at_risk_when_score_is_below_threshold_but_cohort_still_running(): void
    {
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'certificate' => true,
            'certificate_mode' => Course::CERTIFICATE_MODE_SCORE,
            'certificate_score_threshold' => 60,
        ]);
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
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'certificate' => true,
            'certificate_mode' => Course::CERTIFICATE_MODE_SCORE,
            'certificate_score_threshold' => 60,
        ]);
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
        $user = User::factory()->create();
        $course = Course::factory()->create([
            'certificate' => true,
            'certificate_mode' => Course::CERTIFICATE_MODE_BOTH,
            'certificate_attendance_threshold' => 75,
            'certificate_score_threshold' => 60,
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
