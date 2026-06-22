<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Single source of truth for every "dropdown-style" enum surfaced by the
 * public API.
 *
 * The frontend asks the backend for these values via the `Enums` endpoint
 * group so that:
 *   - no enum is hardcoded on the client (one place to add/remove values),
 *   - labels are always localized for the active `Accept-Language`,
 *   - Swagger documents the canonical option set automatically.
 *
 * Add a new enum by appending an entry below. The keys here MUST match the
 * keys defined in `resources/lang/en/enums.php` and `resources/lang/ar/enums.php`.
 *
 * The optional `desc` flag means: also surface a `description` string from
 * `enums.{name}.{value}_desc` when present (used by the "Certificate Basis"
 * radio cards on the Platform Settings page).
 */
final class EnumRegistry
{
    /**
     * @var array<string, array{ values: array<int, string>, desc?: bool }>
     */
    private const MAP = [

        // ── Courses ────────────────────────────────────────────────────
        'course_type' => [
            'values' => ['online', 'offline', 'hybrid', 'external_link'],
        ],
        'course_status' => [
            'values' => ['all', 'pending', 'active', 'upcoming', 'inactive'],
        ],
        'course_level' => [
            'values' => ['beginner', 'intermediate', 'professional'],
        ],
        'cohort_status' => [
            // `scheduled` and `open_for_enrollment` are the only two an
            // admin picks manually; `active`/`completed` are derived from
            // the cohort's start/end dates (see Course::deriveCohortStatus),
            // and `inactive` is the manual "take offline" override.
            'values' => ['scheduled', 'open_for_enrollment', 'active', 'completed', 'inactive'],
        ],
        'module_content_type' => [
            'values' => ['video', 'document', 'article', 'link'],
        ],
        'module_learner_scope' => [
            'values' => ['all', 'cohort'],
        ],

        // ── Resources (LMS knowledge base) ─────────────────────────────
        'resource_type' => [
            'values' => ['article', 'link', 'file'],
        ],

        // ── Platform settings ──────────────────────────────────────────
        'certificate_basis' => [
            'values' => ['attendance', 'score', 'both'],
            'desc'   => true,
        ],
        'locale' => [
            'values' => ['en', 'ar'],
        ],

        // ── Quizzes / assignments ──────────────────────────────────────
        'cohort_scope' => [
            'values' => ['all', 'specific'],
        ],
        'question_type' => [
            'values' => ['mcq', 'yes_no', 'open', 'reorder'],
        ],

        // ── Dashboard ──────────────────────────────────────────────────
        'dashboard_range' => [
            'values' => ['week', 'month', 'quarter', 'year'],
        ],

        // ── Roles ──────────────────────────────────────────────────────
        'role_color' => [
            'values' => ['teal', 'green', 'orange', 'red', 'blue'],
        ],
        'role_guard' => [
            'values' => ['admin', 'web'],
        ],

        // ── Inbox / Messages ───────────────────────────────────────────
        'inbox_tab' => [
            'values' => ['all', 'unread', 'sent', 'resolved'],
        ],

        // ── Users (admin) ──────────────────────────────────────────────
        'user_status' => [
            'values' => ['active', 'inactive', 'deactivated'],
        ],
        'learner_type' => [
            'values' => ['online', 'offline', 'hybrid'],
        ],

        // ── Quiz / assignment lifecycle ────────────────────────────────
        'lifecycle_status' => [
            'values' => ['draft', 'active'],
        ],

        // ── Enrollments / Learners ─────────────────────────────────────
        'enrollment_status' => [
            'values' => ['not_started', 'in_progress', 'completed'],
        ],
    ];

    /**
     * @return array<int, string>
     */
    public static function names(): array
    {
        return array_keys(self::MAP);
    }

    /**
     * @return array<int, string>
     */
    public static function values(string $name): array
    {
        return self::MAP[$name]['values'] ?? [];
    }

    public static function hasDescriptions(string $name): bool
    {
        return (bool) (self::MAP[$name]['desc'] ?? false);
    }

    public static function exists(string $name): bool
    {
        return array_key_exists($name, self::MAP);
    }
}
