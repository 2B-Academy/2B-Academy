<?php

namespace Tests\Feature\Certificates;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UsersCourse;
use App\Services\CertificatePolicy;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\ConfiguresCertificateRule;
use Tests\TestCase;

/**
 * Attendance-based certificate issuance (2026) — the path that lets
 * instructor-led / offline courses mint a real certificate once the learner
 * clears the attendance threshold configured in Platform Config. Previously
 * impossible: issuance only ever fired from exam pass / evaluation submit.
 */
class AttendanceCertificateTest extends TestCase
{
    use RefreshDatabase;
    use ConfiguresCertificateRule;

    private function service(): CertificateService
    {
        return app(CertificateService::class);
    }

    private function attendanceCourse(int $threshold = 75, int $sessions = 4): Course
    {
        $this->configureCertificateRule(CertificatePolicy::BASIS_ATTENDANCE, minAttendance: $threshold);

        return Course::factory()->create([
            'certificate'        => true,
            'number_of_sessions' => $sessions,
            'is_evaluate'        => false,
        ]);
    }

    private function attend(User $user, Course $course, CourseSection $cohort, int $sessionCount): void
    {
        for ($i = 1; $i <= $sessionCount; $i++) {
            Attendance::factory()->create([
                'user_id'    => $user->id,
                'course_id'  => $course->id,
                'section_id' => $cohort->id,
                'session_id' => $i, // distinct sessions attended
            ]);
        }
    }

    public function test_issues_when_attendance_meets_threshold(): void
    {
        $user   = User::factory()->create();
        $course = $this->attendanceCourse(threshold: 75, sessions: 4);
        $cohort = CourseSection::factory()->create(['course_id' => $course->id, 'number_of_sessions' => 4]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        $this->attend($user, $course, $cohort, 3); // 3/4 = 75% ≥ 75

        $certificate = $this->service()->issueFromAttendance($user, $course);

        $this->assertNotNull($certificate);
        $this->assertSame(UserCertificate::SOURCE_ATTENDANCE, $certificate->source_type);
        $this->assertSame(UserCertificate::STATUS_ACTIVE, $certificate->status);
        $this->assertMatchesRegularExpression('/^CERT-\d{4}-\d{6}$/', $certificate->certificate_number);
    }

    public function test_does_not_issue_below_threshold(): void
    {
        $user   = User::factory()->create();
        $course = $this->attendanceCourse(threshold: 75, sessions: 4);
        $cohort = CourseSection::factory()->create(['course_id' => $course->id, 'number_of_sessions' => 4]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        $this->attend($user, $course, $cohort, 2); // 2/4 = 50% < 75

        $this->assertNull($this->service()->issueFromAttendance($user, $course));
        $this->assertDatabaseCount('user_certificates', 0);
    }

    public function test_does_not_issue_when_course_offers_no_certificate(): void
    {
        $user   = User::factory()->create();
        $course = $this->attendanceCourse();
        $course->update(['certificate' => false]);
        $cohort = CourseSection::factory()->create(['course_id' => $course->id, 'number_of_sessions' => 4]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        $this->attend($user, $course, $cohort, 4);

        $this->assertNull($this->service()->issueFromAttendance($user, $course));
    }

    public function test_is_idempotent(): void
    {
        $user   = User::factory()->create();
        $course = $this->attendanceCourse();
        $cohort = CourseSection::factory()->create(['course_id' => $course->id, 'number_of_sessions' => 4]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);
        $this->attend($user, $course, $cohort, 4);

        $a = $this->service()->issueFromAttendance($user, $course);
        $b = $this->service()->issueFromAttendance($user, $course);

        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, UserCertificate::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }
}
