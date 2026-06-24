<?php

namespace App\Http\Resources\Admin;

use App\Http\Traits\HasFile;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * List-view resource for the admin Users overview, matching the Figma
 * table columns:
 *   Name (avatar + email) · Role · Compliance · Status · Last Active
 *
 * The underlying record is a stdClass row produced by AdminUserService's
 * UNION ALL across the `users`, `instructors`, and `admins` tables. Every
 * row exposes a (`source`, `id`) pair so the frontend knows which sub-
 * endpoint to call for follow-up CRUD operations.
 *
 * The legacy "Job Role" field is gone in the 2026 redesign — `job_title`
 * has been dropped from every person table.
 */
class AdminUserListResource extends JsonResource
{
    use HasFile;

    public function toArray(Request $request): array
    {
        $row    = $this->resource;
        $locale = app()->getLocale();

        $nameEn      = (string) ($row->name_en      ?? '');
        $nameAr      = (string) ($row->name_ar      ?? '');
        $nameFallback = (string) ($row->name_fallback ?? '');

        // name_fallback is the raw HR-synced name (typically Arabic).
        // It is used as a last resort so users imported from HR before
        // bilingual sync show their Arabic name for both locales rather
        // than appearing blank.
        $display = $locale === 'ar'
            ? ($nameAr ?: $nameEn ?: $nameFallback)
            : ($nameEn ?: $nameAr ?: $nameFallback);

        $compliance = $row->compliance_pct;
        $compliance = $compliance === null ? null : (int) $compliance;

        $imageField = isset($row->image) ? (string) $row->image : '';
        $imageUrl   = $imageField !== '' ? $this->getFileUrl($imageField) : null;

        return [
            'id'                     => (int)  ($row->id ?? 0),
            'source'                 => (string) ($row->source ?? 'user'),
            'composite_id'           => sprintf('%s:%d', $row->source ?? 'user', (int) ($row->id ?? 0)),
            'name'                   => $display,
            'name_en'                => $nameEn ?: null,
            'name_ar'                => $nameAr ?: null,
            'email'                  => $row->email,
            'phone'                  => $row->phone,
            'machine_code'           => $row->machine_code,
            'department_name'        => $row->department_name,
            'image'                  => $imageUrl ?: null,
            'role'                   => (string) ($row->role_label ?? 'Learner'),
            'role_key'               => (string) ($row->role_key   ?? 'learner'),
            // Real Spatie role machine name + configured colour (teal/green/
            // orange/red/blue) so the badge + filter pills render per-role.
            'role_machine'           => (string) ($row->role_machine ?? ($row->role_key ?? 'learner')),
            'role_color'             => (string) ($row->role_color   ?? 'teal'),
            'status'                 => (string) ($row->status     ?? 'active'),
            // Emit ISO-8601 so the frontend date pipe parses reliably across
            // browsers (a raw "Y-m-d H:i:s" string is rejected by Safari).
            'last_active_at'         => $this->toIso($row->last_active_at ?? null),
            'compliance_pct'         => $compliance,
            'has_compliance'         => $compliance !== null,
            'enrolled_courses_count' => (int) ($row->enrolled_courses_count ?? 0),
            'avatar_initial'         => $this->initial($display ?: 'U'),
            'created_at'             => $row->created_at ? (string) $row->created_at : null,
        ];
    }

    private function initial(string $name): string
    {
        $first = mb_substr(trim($name), 0, 1);
        return $first === '' ? 'U' : mb_strtoupper($first);
    }

    /** Normalise a datetime value to an ISO-8601 string (or null). */
    private function toIso($value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return \Illuminate\Support\Carbon::parse($value)->toIso8601String();
        } catch (\Throwable) {
            return (string) $value;
        }
    }
}
