<?php

namespace App\Services;

use App\Models\Setting;

/**
 * The single source of truth for "how does a learner earn a certificate?".
 *
 * The rule lives in Platform Config (Angular `/admin/settings` → Grading &
 * Certificates), persisted as three `settings` rows:
 *
 *   certificate_award_basis  attendance | score | both
 *   min_passing_attendance   % of cohort sessions the learner must attend
 *   min_passing_score        % of the final exam the learner must reach
 *
 * Before this class those rows were write-only: the admin screen saved them
 * and nothing on the backend ever read them back. Eligibility was instead
 * decided from per-course columns (`courses.certificate_mode`,
 * `certificate_attendance_threshold`, `certificate_score_threshold`) that no
 * API can even set — so the configured rule and the enforced rule were two
 * different things. Everything now reads the rule from here.
 *
 * Division of labour, so the rule is never expressed twice:
 *   - This class owns the RULE (which metrics matter, and at what threshold).
 *   - {@see CertificateProjectionService} owns the MEASUREMENT (turning a
 *     learner+course into an attendance % and a score %), and hands the
 *     numbers back here to be judged.
 *
 * Unmeasurable metrics are SKIPPED, not failed. A course with no session
 * plan cannot produce an attendance percentage, and a course with no final
 * exam cannot produce a score; treating those as failures would mean that
 * flipping the platform to "Attendance only" silently stops every online
 * course from ever certifying. Callers that have no completion trigger of
 * their own (attendance-driven issuance) must additionally require
 * {@see hasEvidence()} so "nothing is measurable" can never mint a
 * certificate on its own.
 */
class CertificatePolicy
{
    public const BASIS_ATTENDANCE = 'attendance';
    public const BASIS_SCORE      = 'score';
    public const BASIS_BOTH       = 'both';

    public const METRIC_ATTENDANCE = 'attendance';
    public const METRIC_SCORE      = 'score';

    public const KEY_BASIS          = 'certificate_award_basis';
    public const KEY_MIN_ATTENDANCE = 'min_passing_attendance';
    public const KEY_MIN_SCORE      = 'min_passing_score';

    /**
     * Fallbacks used only when a settings row is missing entirely. They match
     * the values shipped by PlatformConfigSeeder so a fresh install and a
     * half-seeded install behave identically.
     */
    public const DEFAULT_BASIS          = self::BASIS_ATTENDANCE;
    public const DEFAULT_MIN_ATTENDANCE = 70;
    public const DEFAULT_MIN_SCORE      = 30;

    /** @var array<string, string|null>|null Memoized per request. */
    private ?array $config = null;

    /* ================================================================ *
     |  Configured rule                                                 |
     * ================================================================ */

    public function basis(): string
    {
        $basis = (string) ($this->config()[self::KEY_BASIS] ?? '');

        return in_array($basis, [self::BASIS_ATTENDANCE, self::BASIS_SCORE, self::BASIS_BOTH], true)
            ? $basis
            : self::DEFAULT_BASIS;
    }

    public function minAttendance(): int
    {
        return $this->percent(self::KEY_MIN_ATTENDANCE, self::DEFAULT_MIN_ATTENDANCE);
    }

    public function minScore(): int
    {
        return $this->percent(self::KEY_MIN_SCORE, self::DEFAULT_MIN_SCORE);
    }

    /**
     * Metrics the configured basis requires.
     *
     * @return array<int, string>
     */
    public function requiredMetrics(): array
    {
        return match ($this->basis()) {
            self::BASIS_ATTENDANCE => [self::METRIC_ATTENDANCE],
            self::BASIS_SCORE      => [self::METRIC_SCORE],
            default                => [self::METRIC_ATTENDANCE, self::METRIC_SCORE],
        };
    }

    public function requires(string $metric): bool
    {
        return in_array($metric, $this->requiredMetrics(), true);
    }

    public function threshold(string $metric): int
    {
        return $metric === self::METRIC_ATTENDANCE ? $this->minAttendance() : $this->minScore();
    }

    /* ================================================================ *
     |  Judgement — pure, given already-measured percentages            |
     * ================================================================ */

    /**
     * Verdict per required metric: true = met, false = missed,
     * null = not measurable for this course (skipped).
     *
     * Only required metrics appear as keys, so callers can iterate the
     * result without re-deriving the basis.
     *
     * @return array<string, bool|null>
     */
    public function checks(?int $attendancePercent, ?int $scorePercent): array
    {
        $measured = [
            self::METRIC_ATTENDANCE => $attendancePercent,
            self::METRIC_SCORE      => $scorePercent,
        ];

        $checks = [];
        foreach ($this->requiredMetrics() as $metric) {
            $percent = $measured[$metric];
            $checks[$metric] = $percent === null ? null : $percent >= $this->threshold($metric);
        }

        return $checks;
    }

    /**
     * True when no required metric is actively failing. Unmeasurable metrics
     * are skipped — see the class docblock for why.
     */
    public function isSatisfiedBy(?int $attendancePercent, ?int $scorePercent): bool
    {
        return !in_array(false, $this->checks($attendancePercent, $scorePercent), true);
    }

    /** True when at least one required metric could actually be measured. */
    public function hasEvidence(?int $attendancePercent, ?int $scorePercent): bool
    {
        foreach ($this->checks($attendancePercent, $scorePercent) as $verdict) {
            if ($verdict !== null) {
                return true;
            }
        }

        return false;
    }

    /* ================================================================ *
     |  Internals                                                       |
     * ================================================================ */

    /** Drop the memoized snapshot — used by tests and by the settings writer. */
    public function refresh(): void
    {
        $this->config = null;
    }

    /**
     * Read the three keys in one query.
     *
     * `settings` has historically carried duplicate rows for the same key
     * (see migration 2026_06_02_164052_dedupe_settings_rows_by_key); the
     * lowest id is canonical because that is the row `SettingRepository::
     * updateByKey` edits, so we order by id and keep the first occurrence —
     * matching what the Angular settings screen displays.
     *
     * @return array<string, string|null>
     */
    private function config(): array
    {
        if ($this->config !== null) {
            return $this->config;
        }

        $rows = Setting::query()
            ->whereIn('key', [self::KEY_BASIS, self::KEY_MIN_ATTENDANCE, self::KEY_MIN_SCORE])
            ->orderBy('id')
            ->get(['key', 'value']);

        $config = [];
        foreach ($rows as $row) {
            $config[$row->key] ??= $row->value;
        }

        return $this->config = $config;
    }

    private function percent(string $key, int $default): int
    {
        $raw = $this->config()[$key] ?? null;

        if ($raw === null || trim((string) $raw) === '' || !is_numeric($raw)) {
            return $default;
        }

        return max(0, min(100, (int) $raw));
    }
}
