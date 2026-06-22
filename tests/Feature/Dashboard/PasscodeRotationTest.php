<?php

namespace Tests\Feature\Dashboard;

use App\Models\Course;
use App\Models\CourseSection;
use App\Models\CourseSession;
use App\Models\Setting;
use App\Services\DashboardPasscodeService;
use App\Services\Mobile\SessionPasscodeService;
use Database\Seeders\MobileSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * Dynamic passcode reset / rotation (Figma — "Passcode will reset each …").
 *
 * Verifies the two platform passcode modes and the dashboard "Regenerate"
 * rotation:
 *   • static   (course_attendance_enabled = 1) → code lives for the window
 *   • rotating (course_attendance_enabled = 0) → code expires after
 *     `passcode_reset_seconds` and can be re-issued on the live session.
 */
class PasscodeRotationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MobileSettingSeeder::class);
        Cache::flush();

        // Freeze the clock so the window math is deterministic and never
        // crosses midnight on a slow CI run.
        Carbon::setTestNow(Carbon::today()->setTime(10, 0, 0));
    }

    protected function tearDown(): void
    {
        Carbon::setTestNow();
        parent::tearDown();
    }

    /** Update a mobile setting and bust the cached settings map. */
    private function setSetting(string $key, string $value): void
    {
        Setting::where('key', $key)->update(['value' => $value]);
        Cache::flush();
    }

    private function sessionFor(Course $course, array $attributes = []): CourseSession
    {
        $cohort = CourseSection::factory()->running()->create(['course_id' => $course->id]);

        return CourseSession::factory()->create(array_merge([
            'course_id'    => $course->id,
            'section_id'   => $cohort->id,
            'session_date' => Carbon::now()->toDateString(),
        ], $attributes));
    }

    public function test_static_mode_keeps_passcode_valid_for_the_session_window(): void
    {
        $this->setSetting('course_attendance_enabled', '1');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:30:00', 'time_to' => '12:00:00']);

        app(SessionPasscodeService::class)->issue($session, null, null);

        // Window closes at 12:00 + 15m grace → 12:15, well past now (10:00).
        $expires = Carbon::parse((string) $session->fresh()->passcode_expires_at);
        $this->assertSame('12:15:00', $expires->format('H:i:s'));
    }

    public function test_rotating_mode_expires_after_the_configured_reset_seconds(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '45');

        $course  = Course::factory()->create();
        // No time-of-day bound → the rotation interval is the only limit.
        $session = $this->sessionFor($course, ['time_from' => null, 'time_to' => null]);

        app(SessionPasscodeService::class)->issue($session, null, null);

        $expires = Carbon::parse((string) $session->fresh()->passcode_expires_at);
        $this->assertSame(
            Carbon::now()->addSeconds(45)->toDateTimeString(),
            $expires->toDateTimeString(),
        );
    }

    public function test_rotating_passcode_never_outlives_the_session_window(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        // 1h reset, but the window closes in 20 minutes (+15m grace = 35m).
        $this->setSetting('passcode_reset_seconds', '3600');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:30:00', 'time_to' => '10:20:00']);

        app(SessionPasscodeService::class)->issue($session, null, null);

        $expires = Carbon::parse((string) $session->fresh()->passcode_expires_at);
        $this->assertSame('10:35:00', $expires->format('H:i:s'));
    }

    public function test_regenerate_rotates_the_code_on_the_live_session(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '30');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => null, 'time_to' => null]);

        // Seed an initial code that has already lapsed.
        $session->forceFill([
            'passcode'            => '11111',
            'passcode_issued_at'  => Carbon::now()->subMinutes(5),
            'passcode_expires_at' => Carbon::now()->subMinutes(4),
        ])->save();

        $state = app(DashboardPasscodeService::class)->regenerateCurrent([$course->id]);

        $this->assertTrue($state['available']);
        $this->assertSame('live', $state['state']);
        $this->assertTrue($state['rotates']);
        $this->assertSame(30, $state['reset_seconds']);
        $this->assertNotNull($state['passcode']);
        $this->assertFalse($state['passcode']['expired']);

        // The DB row carries the fresh, still-valid code.
        $fresh = $session->fresh();
        $this->assertSame($state['passcode']['code'], (string) $fresh->passcode);
        $this->assertTrue(Carbon::parse((string) $fresh->passcode_expires_at)->isFuture());
    }

    public function test_regenerate_is_a_no_op_when_no_session_is_live(): void
    {
        $course = Course::factory()->create(); // course with no sessions today

        $state = app(DashboardPasscodeService::class)->regenerateCurrent([$course->id]);

        $this->assertTrue($state['available']);
        $this->assertSame('idle', $state['state']);
        $this->assertNull($state['passcode']);
    }

    public function test_lapsed_rotating_code_stays_live_and_is_reissued(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '120');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:00:00', 'time_to' => '12:00:00']);
        // A previously-issued rotating code that has already lapsed, while
        // the session window (09:00–12:00) is still wide open at 10:00.
        $session->forceFill([
            'passcode'            => '11111',
            'passcode_issued_at'  => Carbon::now()->subMinutes(5),
            'passcode_expires_at' => Carbon::now()->subMinutes(3),
        ])->save();

        $state = app(DashboardPasscodeService::class)->currentState([$course->id]);

        // No "ended" dead-end — it stays live with a freshly re-issued code.
        $this->assertSame('live', $state['state']);
        $this->assertNotNull($state['passcode']);
        $this->assertFalse($state['passcode']['expired']);
        $this->assertTrue(Carbon::parse((string) $session->fresh()->passcode_expires_at)->isFuture());
    }

    public function test_rotating_mode_realigns_a_stale_long_lived_code(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '120');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:00:00', 'time_to' => '12:00:00']);
        // A code with a long (static-style) validity, issued before the
        // platform was switched to rotating mode.
        $session->forceFill([
            'passcode'            => '55555',
            'passcode_issued_at'  => Carbon::now(),
            'passcode_expires_at' => Carbon::now()->addHours(2),
        ])->save();

        $state = app(DashboardPasscodeService::class)->currentState([$course->id]);

        $this->assertSame('live', $state['state']);
        // Realigned down to the 120s rotation interval (no manual restart).
        $expires = Carbon::parse((string) $session->fresh()->passcode_expires_at);
        $this->assertTrue($expires->isFuture());
        $this->assertTrue($expires->lte(Carbon::now()->addSeconds(125)));
    }

    public function test_started_session_with_valid_code_is_live_without_reissue(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '120');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:00:00', 'time_to' => '12:00:00']);
        // Within the rotation interval (100s < 120s) → shown as-is.
        $session->forceFill([
            'passcode'            => '98765',
            'passcode_issued_at'  => Carbon::now(),
            'passcode_expires_at' => Carbon::now()->addSeconds(100),
        ])->save();

        $state = app(DashboardPasscodeService::class)->currentState([$course->id]);

        $this->assertSame('live', $state['state']);
        // Still-valid code is shown as-is (not rotated on read).
        $this->assertSame('98765', $state['passcode']['code']);
    }

    public function test_end_session_revokes_the_code_and_stops_being_live(): void
    {
        $this->setSetting('course_attendance_enabled', '0');

        $course  = Course::factory()->create();
        $session = $this->sessionFor($course, ['time_from' => '09:00:00', 'time_to' => '12:00:00']);
        $session->forceFill([
            'passcode'            => '22222',
            'passcode_issued_at'  => Carbon::now(),
            'passcode_expires_at' => Carbon::now()->addSeconds(30),
        ])->save();

        $state = app(DashboardPasscodeService::class)->endCurrent([$course->id]);

        // Widget reverts to idle with no code.
        $this->assertSame('idle', $state['state']);
        $this->assertNull($state['passcode']);

        // The row's passcode is revoked and the window is closed (past now).
        $fresh = $session->fresh();
        $this->assertNull($fresh->passcode);
        $this->assertNull($fresh->passcode_expires_at);
        $this->assertTrue($fresh->time_to < Carbon::now()->format('H:i:s'));
    }

    public function test_widget_state_exposes_rotation_metadata(): void
    {
        $this->setSetting('course_attendance_enabled', '0');
        $this->setSetting('passcode_reset_seconds', '90');

        $course = Course::factory()->create();
        $this->sessionFor($course, ['time_from' => null, 'time_to' => null]);

        $state = app(DashboardPasscodeService::class)->currentState([$course->id]);

        $this->assertTrue($state['rotates']);
        $this->assertSame(90, $state['reset_seconds']);
    }
}
