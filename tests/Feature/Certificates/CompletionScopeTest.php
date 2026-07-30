<?php

namespace Tests\Feature\Certificates;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\User;
use App\Models\UsersCourse;
use App\Services\Mobile\QualificationProgressService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * The completion vs. finished split (2026) that fixes the "course stuck in
 * Current at 0% AND duplicated in Completed" bug.
 *
 *   - completedCourseIdsForUser  = successfully-earned competency
 *   - finishedCourseIdsForUser   = completed  ∪  enrolments in an ended cohort
 *
 * A session course whose cohort has ended (even with a NULL end_date, so long
 * as all its sessions are in the past) must be "finished" (→ removed from
 * Current, shown in Completed) WITHOUT being counted as a completed
 * competency.
 */
class CompletionScopeTest extends TestCase
{
    use RefreshDatabase;

    private function service(): QualificationProgressService
    {
        return app(QualificationProgressService::class);
    }

    public function test_ended_cohort_with_null_end_date_is_finished_but_not_completed(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => false]);
        // NULL end_date — the exact shape that kept a course perpetually "active".
        $cohort = CourseSection::factory()->create([
            'course_id'  => $course->id,
            'start_date' => now()->subDays(60)->toDateString(),
            'end_date'   => null,
            'status'     => 'scheduled',
        ]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        // All sessions already held (in the past).
        CourseSession::factory()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'session_date' => now()->subDays(10)->toDateString()]);
        CourseSession::factory()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'session_date' => now()->subDays(3)->toDateString()]);

        $finished  = $this->service()->finishedCourseIdsForUser((int) $user->id);
        $completed = $this->service()->completedCourseIdsForUser((int) $user->id);

        $this->assertTrue($finished->contains($course->id), 'ended cohort should be finished');
        $this->assertFalse($completed->contains($course->id), 'ended-but-uncertified course is not a completed competency');
    }

    public function test_running_cohort_is_neither_finished_nor_completed(): void
    {
        $user   = User::factory()->create();
        $course = Course::factory()->create(['certificate' => false]);
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);
        UsersCourse::factory()->create(['user_id' => $user->id, 'course_id' => $course->id, 'group_id' => $cohort->id]);

        // A future session ⇒ cohort is still running.
        CourseSession::factory()->create(['course_id' => $course->id, 'section_id' => $cohort->id, 'session_date' => now()->addDays(5)->toDateString()]);

        $this->assertFalse($this->service()->finishedCourseIdsForUser((int) $user->id)->contains($course->id));
        $this->assertFalse($this->service()->completedCourseIdsForUser((int) $user->id)->contains($course->id));
    }
}
