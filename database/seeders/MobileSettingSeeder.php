<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

/**
 * Mobile App platform settings.
 *
 * Every threshold that drives the Employee mobile API lives in the
 * `settings` table — passcode length, attendance window, page sizes,
 * top-N preview counts, deadline warning days, negative-rating cutoff,
 * etc. The mobile services read them through `App\Services\Mobile\MobileSettings`
 * which falls back to the values seeded here if a row is missing.
 *
 * Each setting carries a `module` tag of the form `mobile_<area>` so the
 * existing admin Settings panel can list and edit them grouped per area
 * without any frontend change.
 *
 * The seeder is idempotent (updateOrCreate keyed by `key` + `module`).
 */
class MobileSettingSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Setting::updateOrCreate(
                ['key' => $row['key'], 'module' => $row['module']],
                $row,
            );
        }

        $this->ensureSharedBearerToken();
    }

    /**
     * Generate the mobile API shared bearer token exactly once.
     *
     * The HR system (and any other integration) must present this
     * token in `Authorization: Bearer <value>` on every mobile API
     * call. We never overwrite an existing value — rotating the token
     * is an explicit admin action (DB update or settings UI), NOT
     * something that re-running the seeder should do silently.
     */
    private function ensureSharedBearerToken(): void
    {
        $row = Setting::firstOrCreate(
            ['key' => 'mobile_shared_bearer_token', 'module' => 'mobile_security'],
            [
                'type'  => 'string',
                'label' => 'Mobile · Security — shared bearer token (HR integration & mobile clients)',
                'value' => Str::random(60),
            ],
        );

        if ($row->wasRecentlyCreated) {
            $this->command?->warn(
                "Mobile shared bearer token created. Store it securely:\n"
                . "    Authorization: Bearer {$row->value}",
            );
        }
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function rows(): array
    {
        return [
            // ────────────────────────────────────────────────────────────
            // Academy listing — S-01 / S-02
            // ────────────────────────────────────────────────────────────
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — courses per page',
                'key'    => 'academy_per_page',
                'value'  => '15',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — search minimum characters',
                'key'    => 'academy_search_min_chars',
                'value'  => '2',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — qualification overflow threshold (show "+N")',
                'key'    => 'academy_qualification_overflow_threshold',
                'value'  => '1',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — deadline soft warning days (orange)',
                'key'    => 'academy_deadline_warning_days',
                'value'  => '7',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — deadline critical warning days (red)',
                'key'    => 'academy_deadline_critical_days',
                'value'  => '2',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — default enrolment-close offset before cohort start (days)',
                'key'    => 'academy_default_close_offset_days',
                'value'  => '0',
                'module' => 'mobile_academy',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Academy — scheduled cohort visibility window before start (days)',
                'key'    => 'academy_scheduled_visibility_days',
                'value'  => '30',
                'module' => 'mobile_academy',
            ],

            // ────────────────────────────────────────────────────────────
            // My Learning — S-05
            // ────────────────────────────────────────────────────────────
            [
                'type'   => 'number',
                'label'  => 'Mobile · My Learning — active courses preview count',
                'key'    => 'my_learning_active_preview_count',
                'value'  => '3',
                'module' => 'mobile_my_learning',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · My Learning — qualifications preview count',
                'key'    => 'my_learning_qualifications_preview_count',
                'value'  => '4',
                'module' => 'mobile_my_learning',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · My Learning — certificates preview count',
                'key'    => 'my_learning_certificates_preview_count',
                'value'  => '2',
                'module' => 'mobile_my_learning',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · My Learning — active courses per page (paginated view)',
                'key'    => 'my_learning_active_per_page',
                'value'  => '15',
                'module' => 'mobile_my_learning',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · My Learning — certificates per page (paginated view)',
                'key'    => 'my_learning_certificates_per_page',
                'value'  => '15',
                'module' => 'mobile_my_learning',
            ],

            // ────────────────────────────────────────────────────────────
            // Attendance — S-06
            // ────────────────────────────────────────────────────────────
            [
                'type'   => 'number',
                'label'  => 'Mobile · Attendance — passcode length (digits)',
                'key'    => 'attendance_passcode_length',
                'value'  => '5',
                'module' => 'mobile_attendance',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Attendance — default validity window (minutes)',
                'key'    => 'attendance_window_minutes',
                'value'  => '30',
                'module' => 'mobile_attendance',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Attendance — session "starts soon" buffer (minutes before time_from)',
                'key'    => 'attendance_session_open_buffer_minutes',
                'value'  => '15',
                'module' => 'mobile_attendance',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Attendance — session "still open" buffer (minutes after time_to)',
                'key'    => 'attendance_session_grace_minutes',
                'value'  => '15',
                'module' => 'mobile_attendance',
            ],
            [
                // Yes  → the passcode stays the same for the whole session.
                // No   → the passcode rotates every `passcode_reset_seconds`.
                'type'   => 'boolean',
                'label'  => 'Mobile · Attendance — keep the passcode static for the whole session',
                'key'    => 'course_attendance_enabled',
                'value'  => '1',
                'module' => 'mobile_attendance',
            ],
            [
                // Only relevant when `course_attendance_enabled` is off: how
                // often the live passcode resets (the dashboard widget counts
                // down to this and re-issues a fresh code, so a stale code is
                // never accepted on the mobile S-06 Mark Present screen).
                'type'   => 'number',
                'label'  => 'Mobile · Attendance — passcode reset interval (seconds, rotating mode)',
                'key'    => 'passcode_reset_seconds',
                'value'  => '30',
                'module' => 'mobile_attendance',
            ],

            // ────────────────────────────────────────────────────────────
            // Ratings (cohort feedback)
            // ────────────────────────────────────────────────────────────
            [
                'type'   => 'number',
                'label'  => 'Mobile · Rating — minimum value',
                'key'    => 'rating_min_value',
                'value'  => '1',
                'module' => 'mobile_rating',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Rating — maximum value',
                'key'    => 'rating_max_value',
                'value'  => '5',
                'module' => 'mobile_rating',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Rating — comment required when rating ≤ this value',
                'key'    => 'rating_comment_required_at_or_below',
                'value'  => '3',
                'module' => 'mobile_rating',
            ],
            [
                'type'   => 'number',
                'label'  => 'Mobile · Rating — comment max length (chars)',
                'key'    => 'rating_comment_max_length',
                'value'  => '2000',
                'module' => 'mobile_rating',
            ],
        ];
    }
}
