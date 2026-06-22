<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Course;
use App\Models\CourseExam;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\JobTitle;
use App\Models\QualificationSkill;
use App\Models\User;
use App\Models\UserExam;
use App\Models\UsersCourse;

/**
 * S-05 (My Learning: overview / active / qualifications / sessions) and
 * the S-07 certificate listing.
 */
class MyLearningApiTest extends MobileTestCase
{
    /** Enrol the user into a currently-running cohort of a fresh course. */
    private function enrolInRunningCourse(User $user): array
    {
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);

        return [$course, $cohort];
    }

    // ── overview ────────────────────────────────────────────────────

    public function test_overview_returns_counts_and_previews(): void
    {
        $user = $this->employee();
        $this->enrolInRunningCourse($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/overview');

        $this->assertSuccess($response);
        $response->assertJsonStructure(['result' => [
            'learner',
            'counts'   => ['active_courses', 'qualifications', 'certificates'],
            'previews' => ['active_courses', 'qualifications', 'certificates'],
        ]])->assertJsonPath('result.counts.active_courses', 1);
    }

    // ── active ──────────────────────────────────────────────────────

    public function test_active_lists_enrolled_running_course(): void
    {
        $user = $this->employee();
        [$course] = $this->enrolInRunningCourse($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/active');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonPath('result.0.id', $course->id)
                 ->assertJsonStructure(['result' => [[
                     'id', 'title', 'cohort', 'progress' => ['percent', 'total_lectures', 'total_sessions'],
                 ]]]);
    }

    public function test_active_is_empty_for_unenrolled_learner(): void
    {
        $user = $this->employee();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/active');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 0);
    }

    // ── qualifications ──────────────────────────────────────────────

    public function test_qualifications_empty_without_job_title(): void
    {
        $user = $this->employee(['job_title_id' => null]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/qualifications');

        $this->assertSuccess($response);
        $response->assertJsonCount(0, 'result');
    }

    public function test_qualifications_reports_progress_for_job_title(): void
    {
        $jobTitle = JobTitle::factory()->create();
        $skill    = QualificationSkill::factory()->create();
        $course   = Course::factory()->create();

        $jobTitle->qualificationSkills()->attach($skill->id);
        $skill->courses()->attach($course->id);

        $user = $this->employee(['job_title_id' => $jobTitle->id]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/qualifications');

        $this->assertSuccess($response);
        $response->assertJsonCount(1, 'result')
                 ->assertJsonPath('result.0.total_courses', 1)
                 ->assertJsonPath('result.0.completed_courses', 0)
                 ->assertJsonStructure(['result' => [['id', 'name', 'total_courses', 'completed_courses', 'percent']]]);
    }

    // ── sessions ────────────────────────────────────────────────────

    public function test_sessions_returns_attendance_log_for_enrolled_course(): void
    {
        $user = $this->employee();
        [$course, $cohort] = $this->enrolInRunningCourse($user);
        CourseSession::factory()->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/courses/' . $course->id . '/sessions');

        $this->assertSuccess($response);
        $response->assertJsonCount(1, 'result')
                 ->assertJsonStructure(['result' => [['id', 'title', 'session_date', 'attended']]])
                 ->assertJsonPath('result.0.attended', false);
    }

    public function test_sessions_forbidden_when_not_enrolled(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/courses/' . $course->id . '/sessions');

        $this->assertError($response, 403);
    }

    // ── certificates list (S-07) ────────────────────────────────────

    public function test_certificates_list_includes_passed_final_exam(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create(['certificate' => true, 'is_evaluate' => false]);
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

        // Certificates are first-class rows now — issue it the same way
        // the live exam-submit hook does.
        app(\App\Services\CertificateService::class)->issueFromExam($userExam);

        $response = $this->withHeaders($this->headersFor($user))
                         ->getJson(self::BASE . '/mobile/my-learning/certificates');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1)
                 ->assertJsonStructure(['result' => [['id', 'uuid', 'certificate_number', 'status', 'course_id', 'course_title', 'issued_at']]]);
    }
}
