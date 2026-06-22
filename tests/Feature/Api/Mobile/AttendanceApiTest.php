<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UsersCourse;

/**
 * S-06 — Mark Present via instructor passcode.
 */
class AttendanceApiTest extends MobileTestCase
{
    private const PASSCODE = '12345';

    /**
     * Enrol the learner and return [course, cohort, openSession] where the
     * session is open for attendance with a live passcode.
     */
    private function openSessionFor(User $user): array
    {
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);
        $session = CourseSession::factory()->openForAttendance(self::PASSCODE)->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);

        return [$course, $cohort, $session];
    }

    public function test_mark_present_succeeds_with_valid_passcode(): void
    {
        $user = $this->employee();
        [$course, $cohort, $session] = $this->openSessionFor($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => self::PASSCODE,
                         ]);

        $this->assertSuccess($response, 201);
        $this->assertDatabaseHas('attendances', [
            'user_id'    => $user->id,
            'course_id'  => $course->id,
            'session_id' => $session->id,
        ]);
    }

    public function test_mark_present_with_wrong_passcode_returns_422(): void
    {
        $user = $this->employee();
        [$course, , $session] = $this->openSessionFor($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => '54321',
                         ]);

        $this->assertError($response, 422);
        $this->assertDatabaseMissing('attendances', [
            'user_id'   => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_mark_present_when_not_enrolled_returns_403(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        $session = CourseSession::factory()->openForAttendance(self::PASSCODE)->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => self::PASSCODE,
                         ]);

        $this->assertError($response, 403);
    }

    public function test_mark_present_with_no_open_window_returns_409(): void
    {
        $user   = $this->employee();
        $course = Course::factory()->create();
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create([
            'user_id'   => $user->id,
            'course_id' => $course->id,
            'group_id'  => $cohort->id,
        ]);
        // Session with no passcode issued → not open for attendance.
        $session = CourseSession::factory()->create([
            'course_id'  => $course->id,
            'section_id' => $cohort->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => self::PASSCODE,
                         ]);

        $this->assertError($response, 409);
    }

    public function test_mark_present_twice_returns_409_already_marked(): void
    {
        $user = $this->employee();
        [$course, , $session] = $this->openSessionFor($user);

        // Pre-existing attendance for this session.
        Attendance::factory()->create([
            'user_id'    => $user->id,
            'course_id'  => $course->id,
            'session_id' => $session->id,
        ]);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => self::PASSCODE,
                         ]);

        $this->assertError($response, 409);
    }

    public function test_mark_present_validation_requires_passcode(): void
    {
        $user = $this->employee();
        [$course, , $session] = $this->openSessionFor($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                         ]);

        $this->assertError($response, 422);
    }

    public function test_mark_present_validation_rejects_wrong_length_passcode(): void
    {
        $user = $this->employee();
        [$course, , $session] = $this->openSessionFor($user);

        $response = $this->withHeaders($this->headersFor($user))
                         ->postJson(self::BASE . '/mobile/attendance/mark', [
                             'course_id'  => $course->id,
                             'session_id' => $session->id,
                             'passcode'   => '123',
                         ]);

        $this->assertError($response, 422);
    }
}
