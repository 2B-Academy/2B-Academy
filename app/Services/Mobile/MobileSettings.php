<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Strongly-typed accessor for every mobile-tunable threshold.
 *
 * All values originate from the `settings` table (seeded by
 * MobileSettingSeeder under modules `mobile_academy`,
 * `mobile_my_learning`, `mobile_attendance`, `mobile_rating`).
 * Nothing in the mobile services hardcodes a number — they all flow
 * through this class so a single SQL UPDATE can re-tune the platform.
 *
 * The reads are cached for the duration of a request (the result of
 * `getAll()` is memoized in the platform cache for 10 minutes —
 * matching the existing `cms.settings.map` cache pattern in
 * AppServiceProvider). Mutating any mobile setting from the admin
 * panel is expected to flush `mobile.settings.map`.
 *
 * Every accessor takes a *guaranteed-positive* fallback closure
 * (`?? throw`) because absence here is a deploy/seeding bug — not
 * something the user should silently see in production.
 */
final class MobileSettings
{
    private const CACHE_KEY = 'mobile.settings.map';
    private const CACHE_TTL_MINUTES = 10;

    private const ALL_CACHE_KEY = 'settings.all.map';

    /**
     * Flat `key => value` map of every `mobile_*` module setting.
     *
     * @var array<string, string>|null
     */
    private ?array $map = null;

    /**
     * Flat `key => value` map of EVERY setting (all modules), preferring the
     * `platform` row on key collisions. Used for the handful of values that
     * the admin `/admin/settings` (Platform Config) screen owns but the
     * mobile layer still needs to read — e.g. the attendance passcode mode.
     *
     * @var array<string, string>|null
     */
    private ?array $allMap = null;

    public function flush(): void
    {
        $this->map = null;
        $this->allMap = null;
        Cache::forget(self::CACHE_KEY);
        Cache::forget(self::ALL_CACHE_KEY);
    }

    // ────────────────────────────────────────────────────────────
    // Academy (S-01 / S-02)
    // ────────────────────────────────────────────────────────────

    public function academyPerPage(): int
    {
        return $this->positiveInt('academy_per_page');
    }

    public function academySearchMinChars(): int
    {
        return $this->nonNegativeInt('academy_search_min_chars');
    }

    public function academyQualificationOverflowThreshold(): int
    {
        return $this->nonNegativeInt('academy_qualification_overflow_threshold');
    }

    public function academyDeadlineWarningDays(): int
    {
        return $this->nonNegativeInt('academy_deadline_warning_days');
    }

    public function academyDeadlineCriticalDays(): int
    {
        return $this->nonNegativeInt('academy_deadline_critical_days');
    }

    public function academyDefaultCloseOffsetDays(): int
    {
        return $this->nonNegativeInt('academy_default_close_offset_days');
    }

    /**
     * How many days before its start date a `scheduled` cohort becomes
     * visible in the Academy (Figma 332:10708 — "automatically open 1
     * month before the start date"). `open_for_enrollment` cohorts ignore
     * this and appear immediately.
     *
     * Reads a safe fallback (30 ≈ one month) when the setting row hasn't
     * been seeded yet so the Academy never goes blank on an un-migrated
     * environment.
     */
    public function academyScheduledVisibilityDays(): int
    {
        return $this->nonNegativeIntOr('academy_scheduled_visibility_days', 30);
    }

    // ────────────────────────────────────────────────────────────
    // My Learning (S-05)
    // ────────────────────────────────────────────────────────────

    public function myLearningActivePreviewCount(): int
    {
        return $this->positiveInt('my_learning_active_preview_count');
    }

    public function myLearningQualificationsPreviewCount(): int
    {
        return $this->positiveInt('my_learning_qualifications_preview_count');
    }

    public function myLearningCertificatesPreviewCount(): int
    {
        return $this->positiveInt('my_learning_certificates_preview_count');
    }

    public function myLearningActivePerPage(): int
    {
        return $this->positiveInt('my_learning_active_per_page');
    }

    public function myLearningCertificatesPerPage(): int
    {
        return $this->positiveInt('my_learning_certificates_per_page');
    }

    // ────────────────────────────────────────────────────────────
    // Attendance (S-06)
    // ────────────────────────────────────────────────────────────

    public function attendancePasscodeLength(): int
    {
        return $this->positiveInt('attendance_passcode_length');
    }

    public function attendanceWindowMinutes(): int
    {
        return $this->positiveInt('attendance_window_minutes');
    }

    public function attendanceSessionOpenBufferMinutes(): int
    {
        return $this->nonNegativeInt('attendance_session_open_buffer_minutes');
    }

    public function attendanceSessionGraceMinutes(): int
    {
        return $this->nonNegativeInt('attendance_session_grace_minutes');
    }

    /**
     * When true the issued passcode stays valid for the entire session
     * window ("Course Attendance = Yes" → "The passcode will remain the
     * same for the entire session"). When false the passcode rotates
     * every `passcodeResetSeconds()` and must be re-issued by the
     * instructor dashboard (Figma — "Passcode will reset each …").
     *
     * Defaults to the static behaviour on un-seeded environments so an
     * older deployment keeps its previous "valid for the window" semantics.
     *
     * NOTE: this key is owned by Platform Config (module `platform`) and
     * edited from the dashboard Settings screen — NOT the `mobile_*` modules
     * — so it is resolved through {@see platformValue()} which reads across
     * every module. Reading it from the mobile-only map was the cause of the
     * "passcode never resets" bug: the dashboard wrote the `platform` row
     * while this class kept reading a stale `mobile_attendance` copy.
     */
    public function passcodeStaticForSession(): bool
    {
        $value = $this->platformValue('course_attendance_enabled');

        if ($value === null || $value === '') {
            return true; // un-seeded → keep the legacy "static for window" behaviour
        }

        return in_array(strtolower($value), ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Rotation interval (seconds) for the live passcode when it is NOT
     * static. Falls back to 30s on un-seeded environments and clamps to a
     * sane floor so a misconfigured `0` never produces an already-expired
     * code.
     *
     * Owned by Platform Config (module `platform`) — resolved globally via
     * {@see platformValue()} so dashboard edits take effect immediately.
     */
    public function passcodeResetSeconds(): int
    {
        $value = $this->platformValue('passcode_reset_seconds');

        $seconds = ($value === null || $value === '') ? 30 : (int) $value;

        return $seconds > 0 ? $seconds : 30;
    }

    // ────────────────────────────────────────────────────────────
    // Rating
    // ────────────────────────────────────────────────────────────

    public function ratingMinValue(): int
    {
        return $this->nonNegativeInt('rating_min_value');
    }

    public function ratingMaxValue(): int
    {
        return $this->positiveInt('rating_max_value');
    }

    public function ratingCommentRequiredAtOrBelow(): int
    {
        return $this->nonNegativeInt('rating_comment_required_at_or_below');
    }

    public function ratingCommentMaxLength(): int
    {
        return $this->positiveInt('rating_comment_max_length');
    }

    // ────────────────────────────────────────────────────────────
    // Security (S2S integration)
    // ────────────────────────────────────────────────────────────

    /**
     * Shared bearer token that every mobile API caller (HR system,
     * partner integrations, …) must present in
     * `Authorization: Bearer <token>`.
     *
     * Rotate by issuing a single `UPDATE settings ...` (or via the
     * admin settings UI) and calling `MobileSettings::flush()` —
     * existing clients must then be updated with the new value.
     */
    public function sharedBearerToken(): string
    {
        $map = $this->loadMap();

        if (!array_key_exists('mobile_shared_bearer_token', $map)) {
            throw new \RuntimeException(
                'Missing mobile setting `mobile_shared_bearer_token`. '
                .'Run `php artisan db:seed --class=MobileSettingSeeder`.',
            );
        }

        $value = (string) $map['mobile_shared_bearer_token'];

        if ($value === '') {
            throw new \RuntimeException(
                'Mobile shared bearer token is empty in the settings table.',
            );
        }

        return $value;
    }

    // ────────────────────────────────────────────────────────────
    // Internals
    // ────────────────────────────────────────────────────────────

    private function positiveInt(string $key): int
    {
        $value = $this->intValue($key);

        if ($value <= 0) {
            throw new \RuntimeException(
                "Mobile setting `{$key}` must be a positive integer (got {$value}). "
                ."Run `php artisan db:seed --class=MobileSettingSeeder`.",
            );
        }

        return $value;
    }

    private function nonNegativeInt(string $key): int
    {
        $value = $this->intValue($key);

        if ($value < 0) {
            throw new \RuntimeException(
                "Mobile setting `{$key}` must be a non-negative integer (got {$value}).",
            );
        }

        return $value;
    }

    /**
     * Like nonNegativeInt() but tolerates a missing/blank row, returning
     * the supplied default instead of throwing. Used for settings added
     * after initial seeding so older environments degrade gracefully.
     */
    private function nonNegativeIntOr(string $key, int $default): int
    {
        $map = $this->loadMap();

        if (!array_key_exists($key, $map) || $map[$key] === '' || $map[$key] === null) {
            return $default;
        }

        $value = (int) $map[$key];

        return $value >= 0 ? $value : $default;
    }

    /**
     * Boolean accessor that tolerates a missing/blank row (returning the
     * supplied default). Accepts the usual truthy spellings ("1", "true",
     * "yes", "on") case-insensitively.
     */
    private function boolOr(string $key, bool $default): bool
    {
        $map = $this->loadMap();

        if (!array_key_exists($key, $map) || $map[$key] === '' || $map[$key] === null) {
            return $default;
        }

        return in_array(strtolower((string) $map[$key]), ['1', 'true', 'yes', 'on'], true);
    }

    /**
     * Resolve a setting value by key across EVERY module (not just `mobile_*`).
     *
     * Used for keys that the admin Platform Config screen owns but the mobile
     * / passcode layer also consumes (the attendance passcode mode + reset
     * interval). It deliberately reads the SAME row the admin save targets:
     * `SettingRepository::updateByKey()` upserts via `firstOrNew(['key' => …])`
     * — i.e. the lowest-id row for that key — so we read lowest-id-first too.
     * This keeps reads and writes pointing at one row even if a duplicate key
     * exists in more than one module, which was the "passcode never resets"
     * bug (dashboard wrote the `platform` row; this class read a stale
     * `mobile_attendance` copy).
     *
     * Returns null when the key is absent. Cached for the request + 10 minutes
     * and flushed by {@see flush()} (and by SettingService on every save).
     */
    private function platformValue(string $key): ?string
    {
        if ($this->allMap === null) {
            $this->allMap = Cache::remember(
                self::ALL_CACHE_KEY,
                now()->addMinutes(self::CACHE_TTL_MINUTES),
                static function (): array {
                    $out = [];

                    // Ascending id == the row firstOrNew() resolves on write, so
                    // the first value we keep per key is the canonical one.
                    foreach (
                        Setting::query()->orderBy('id')->get(['key', 'value']) as $row
                    ) {
                        if (!array_key_exists($row->key, $out)) {
                            $out[$row->key] = $row->value;
                        }
                    }

                    return $out;
                },
            );
        }

        $value = $this->allMap[$key] ?? null;

        return $value === null ? null : (string) $value;
    }

    private function intValue(string $key): int
    {
        $map = $this->loadMap();

        if (!array_key_exists($key, $map)) {
            throw new \RuntimeException(
                "Missing mobile setting `{$key}`. "
                ."Run `php artisan db:seed --class=MobileSettingSeeder`.",
            );
        }

        return (int) $map[$key];
    }

    /**
     * @return array<string, string>
     */
    private function loadMap(): array
    {
        if ($this->map !== null) {
            return $this->map;
        }

        $this->map = Cache::remember(
            self::CACHE_KEY,
            now()->addMinutes(self::CACHE_TTL_MINUTES),
            static fn (): array => Setting::query()
                ->where('module', 'LIKE', 'mobile\_%')
                ->pluck('value', 'key')
                ->all(),
        );

        return $this->map;
    }
}
