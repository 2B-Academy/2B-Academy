<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

/**
 * Seeds the Platform Settings keys consumed by the Angular admin
 * `/admin/settings` (Platform Config) page.
 *
 * Only inserts rows that don't already exist — existing values are left
 * untouched so admin edits survive `db:seed --class=PlatformConfigSeeder`.
 */
class PlatformConfigSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->rows() as $row) {
            Setting::query()->firstOrCreate(['key' => $row['key']], $row);
        }
    }

    /** @return array<int, array<string, mixed>> */
    private function rows(): array
    {
        $module = 'platform';

        return [
            // ── General ───────────────────────────────────────────────
            ['key' => 'platform_name',     'value' => 'NAS LMS', 'type' => 'text',     'label' => 'Platform Name',     'module' => $module],
            ['key' => 'default_language',  'value' => 'en',      'type' => 'text',     'label' => 'Default Language',  'module' => $module],

            // ── Enrolment & Attendance ────────────────────────────────
            ['key' => 'default_cohort_size',     'value' => '30', 'type' => 'number',  'label' => 'Default Cohort Size',     'module' => $module],
            ['key' => 'course_attendance_enabled','value' => '1', 'type' => 'boolean', 'label' => 'Course Attendance',       'module' => $module],
            ['key' => 'passcode_reset_seconds',  'value' => '30', 'type' => 'number',  'label' => 'Passcode Reset (seconds)','module' => $module],

            // ── Grading & Certificates ────────────────────────────────
            ['key' => 'course_ratings_enabled',     'value' => '1',  'type' => 'boolean', 'label' => 'Course Ratings',            'module' => $module],
            ['key' => 'abnormal_rating_threshold',  'value' => '30', 'type' => 'number',  'label' => 'Abnormal Rating Threshold', 'module' => $module],
            // attendance | score | both
            ['key' => 'certificate_award_basis',    'value' => 'attendance', 'type' => 'text',   'label' => 'Certificate Awarded Based On', 'module' => $module],
            ['key' => 'min_passing_score',          'value' => '30',         'type' => 'number', 'label' => 'Min Passing Score (%)',         'module' => $module],

            // ── About Us (rich text + image) ──────────────────────────
            ['key' => 'about_description', 'value' => '', 'type' => 'textarea', 'label' => 'About — Description', 'module' => $module],
            ['key' => 'about_values',      'value' => '', 'type' => 'textarea', 'label' => 'About — Our Values',  'module' => $module],
            ['key' => 'about_mission',     'value' => '', 'type' => 'textarea', 'label' => 'About — Our Mission', 'module' => $module],
            ['key' => 'about_vision',      'value' => '', 'type' => 'textarea', 'label' => 'About — Our Vision',  'module' => $module],
            ['key' => 'about_image',       'value' => '', 'type' => 'file',     'label' => 'About — Image',       'module' => $module],
            // header_logo, banner_background, footer_logo, banner_description, why_us already exist via SettingSeeder.
        ];
    }
}
