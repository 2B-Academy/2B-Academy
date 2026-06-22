<?php

namespace Database\Seeders;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\User;
use App\Models\UserCourseEvaluation;
use App\Models\UserExam;
use App\Services\CertificateService;
use Illuminate\Database\Seeder;

/**
 * php artisan db:seed --class=Demo2394CertificateSeeder
 *
 * Guarantees that the demo learner (machine_code = 2394) holds at least
 * two ACTIVE certificates — one earned via a passed final exam and one
 * via a completed evaluation — so the full flow can be exercised end to
 * end from both the admin dashboard and the mobile app.
 *
 * Fully idempotent: re-running never duplicates (issuance is funneled
 * through CertificateService, which enforces one active certificate per
 * learner+course).
 */
class Demo2394CertificateSeeder extends Seeder
{
    public function run(): void
    {
        /** @var CertificateService $service */
        $service = app(CertificateService::class);

        $user = User::where('machine_code', '2394')->first();
        if ($user === null) {
            $this->command?->warn('No user with machine_code = 2394; skipping demo certificate seed.');
            return;
        }

        $this->seedExamCertificate($service, $user);
        $this->seedEvaluationCertificate($service, $user);

        $this->command?->info("Demo certificates ready for {$user->name} (2394).");
    }

    private function seedExamCertificate(CertificateService $service, User $user): void
    {
        $course = Course::where('certificate', true)
            ->where('is_evaluate', false)
            ->first()
            ?? Course::factory()->create([
                'certificate' => true,
                'is_evaluate' => false,
            ]);

        $exam = CourseExam::where('course_id', $course->id)->where('is_final', true)->first();
        if ($exam === null) {
            $cohort = CourseSection::where('course_id', $course->id)->first()
                ?? CourseSection::factory()->create(['course_id' => $course->id]);
            $exam = CourseExam::factory()->final()->create([
                'course_id'  => $course->id,
                'section_id' => $cohort->id,
            ]);
        }

        $userExam = UserExam::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id, 'exam_id' => $exam->id],
            ['status' => 'success', 'user_degree' => $exam->degree ?? 100],
        );

        if ($userExam->status !== 'success') {
            $userExam->update(['status' => 'success']);
        }

        $service->issueFromExam($userExam);
    }

    private function seedEvaluationCertificate(CertificateService $service, User $user): void
    {
        $course = Course::where('certificate', true)
            ->where('is_evaluate', true)
            ->first()
            ?? Course::factory()->create([
                'certificate' => true,
                'is_evaluate' => true,
            ]);

        $evaluation = UserCourseEvaluation::firstOrCreate(
            ['user_id' => $user->id, 'course_id' => $course->id],
            ['instructor_id' => 1, 'evaluation_id' => 1, 'answer' => 'demo'],
        );

        $service->issueFromEvaluation($evaluation);
    }
}
