<?php

namespace Tests\Feature\Certificates;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCertificate;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Services\CertificateService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Domain rules for the first-class certificate entity: eligibility,
 * numbering, the one-active-per-learner+course invariant, and revocation.
 */
class CertificateIssuanceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): CertificateService
    {
        return app(CertificateService::class);
    }

    private function passedFinalExam(User $user, Course $course): UserExam
    {
        $cohort = CourseSection::factory()->create(['course_id' => $course->id]);
        $exam   = CourseExam::factory()->final()->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);

        return UserExam::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'exam_id'   => $exam->id,
            'status'    => 'success',
        ]);
    }

    public function test_issue_from_exam_creates_active_numbered_certificate(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => false]);

        $certificate = $this->service()->issueFromExam($this->passedFinalExam($user, $course));

        $this->assertNotNull($certificate);
        $this->assertSame(UserCertificate::STATUS_ACTIVE, $certificate->status);
        $this->assertSame('exam', $certificate->source_type);
        $this->assertMatchesRegularExpression('/^CERT-\d{4}-\d{6}$/', $certificate->certificate_number);
        $this->assertNotNull($certificate->uuid);
        $this->assertDatabaseHas('user_certificates', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'status'    => 'active',
        ]);
    }

    public function test_numbers_are_sequential_within_a_year(): void
    {
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => false]);

        $first  = $this->service()->issueFromExam($this->passedFinalExam(User::factory()->create(), $course));
        $second = $this->service()->issueFromExam($this->passedFinalExam(User::factory()->create(), $course));

        $year = now()->year;
        $this->assertSame(sprintf('CERT-%d-000001', $year), $first->certificate_number);
        $this->assertSame(sprintf('CERT-%d-000002', $year), $second->certificate_number);
    }

    public function test_issuance_is_idempotent_per_learner_and_course(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => false]);
        $exam   = $this->passedFinalExam($user, $course);

        $a = $this->service()->issueFromExam($exam);
        $b = $this->service()->issueFromExam($exam);

        $this->assertSame($a->id, $b->id);
        $this->assertSame(1, UserCertificate::where('user_id', $user->id)->where('course_id', $course->id)->count());
    }

    public function test_no_certificate_when_course_does_not_allow_it(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => false, 'is_evaluate' => false]);

        $this->assertNull($this->service()->issueFromExam($this->passedFinalExam($user, $course)));
        $this->assertDatabaseCount('user_certificates', 0);
    }

    public function test_issue_from_evaluation_creates_certificate(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => true]);

        $evaluation = UserCourseEvaluation::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);

        $certificate = $this->service()->issueFromEvaluation($evaluation);

        $this->assertNotNull($certificate);
        $this->assertSame('evaluation', $certificate->source_type);
        $this->assertSame(UserCertificate::STATUS_ACTIVE, $certificate->status);
    }

    public function test_revoke_keeps_row_and_sets_status(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => false]);
        $certificate = $this->service()->issueFromExam($this->passedFinalExam($user, $course));

        $admin = User::factory()->create();
        $revoked = $this->service()->revoke($certificate, (int) $admin->id);

        $this->assertSame(UserCertificate::STATUS_REVOKED, $revoked->status);
        $this->assertNotNull($revoked->revoked_at);
        $this->assertSame((int) $admin->id, (int) $revoked->revoked_by);
        $this->assertDatabaseHas('user_certificates', [
            'id'     => $certificate->id,
            'status' => 'revoked',
        ]);
    }
}
