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

        $nameEn = (string) ($row->name_en ?? '');
        $nameAr = $row->name_ar !== null ? (string) $row->name_ar : null;
        $display = $locale === 'ar'
            ? ($nameAr ?: $nameEn)
            : ($nameEn ?: ($nameAr ?? ''));

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
            'name_ar'                => $nameAr,
            'email'                  => $row->email,
            'phone'                  => $row->phone,
            'machine_code'           => $row->machine_code,
            'department_name'        => $row->department_name,
            'image'                  => $imageUrl ?: null,
            'role'                   => (string) ($row->role_label ?? 'Learner'),
            'role_key'               => (string) ($row->role_key   ?? 'learner'),
            'status'                 => (string) ($row->status     ?? 'active'),
            'last_active_at'         => $row->last_active_at ? (string) $row->last_active_at : null,
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
}
