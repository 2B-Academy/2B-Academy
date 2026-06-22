<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\UserExam;
use App\Services\CertificateService;

/**
 * S-07 — certificate detail + download. The list itself is covered in
 * MyLearningApiTest; here we exercise the per-certificate lookup by its
 * own integer id (the compound-id pattern has been removed).
 */
class CertificateApiTest extends MobileTestCase
{
    /** Build a passed-final-exam certificate and return [user, certificateId]. */
    private function certificateFor(): array
    {
        $user   = $this->employee(['name' => 'Cert Holder']);
        $course = Course::factory()->create([
            'certificate'  => true,
            'is_evaluate'  => false,
        ]);
        $cohort = CourseSection::factory()->create(['course_id' => $course->id]);
        $exam   = CourseExam::factory()->final()->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);
        $userExam = UserExam::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'exam_id'   => $exam->id,
            'status'    => 'success',
        ]);

        $certificate = app(CertificateService::class)->issueFromExam($userExam);

        return [$user, (int) $certificate->id];
    }

    public function test_certificate_detail_returns_payload(): void
    {
        [$user, $certificateId] = $this->certificateFor();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/certificates/' . $certificateId);

        $this->assertSuccess($response);
        $response->assertJsonPath('result.id', $certificateId)
                 ->assertJsonStructure(['result' => ['id', 'uuid', 'certificate_number', 'status', 'course_id', 'course_title', 'issued_at']]);
    }

    public function test_certificate_download_returns_base64_image(): void
    {
        [$user, $certificateId] = $this->certificateFor();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/certificates/' . $certificateId . '/download');

        $this->assertSuccess($response);
        $response->assertJsonPath('result.mime_type', 'image/jpeg')
                 ->assertJsonStructure(['result' => ['id', 'certificate_number', 'course_id', 'course_title', 'image_base64', 'mime_type']]);
        $this->assertNotEmpty($response->json('result.image_base64'));
    }

    public function test_certificate_detail_404_for_unknown_certificate(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/certificates/999999');

        $this->assertError($response, 404);
    }

    public function test_certificate_not_visible_to_other_learner(): void
    {
        [, $certificateId] = $this->certificateFor();
        $other = $this->employee();

        $response = $this->withHeaders($this->headersFor($other))
                         ->getJson(self::BASE . '/mobile/certificates/' . $certificateId);

        $this->assertError($response, 404);
    }
}
