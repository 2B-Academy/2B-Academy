<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $locale = app()->getLocale();

        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'email'          => $this->email,
            'roles'          => $this->whenLoaded('roles', fn () => $this->roles->pluck('name')),

            // -----------------------------------------------------------
            // Dynamic admin authorization payload (2026 Roles redesign).
            //
            // `view_keys` lists every `view-*` permission the admin holds
            // through the roles assigned to them. The frontend uses it to
            // gate sidebar items and route activation. Falls back to an
            // empty list when the roles relation isn't loaded — never
            // null, so the client never has to defensive-check.
            // -----------------------------------------------------------
            'view_keys'      => $this->viewKeys(),
            'is_super_admin' => $this->isSuperAdmin(),

            // Display payload for table badges — see roleChip() below.
            'role_chip'      => $this->roleChip($locale),

            'created_at'     => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Distinct `view-*` permission keys aggregated from every role
     * assigned to this admin.
     *
     * Only emits when BOTH `roles` and `roles.permissions` are eager-
     * loaded. The list endpoint (`with('roles')` only) intentionally
     * returns `[]` — that path doesn't need view_keys per row and we
     * must not trigger a lazy load and cause an N+1 across a paginated
     * page.
     *
     * @return array<int,string>
     */
    private function viewKeys(): array
    {
        if (!$this->relationLoaded('roles')) {
            return [];
        }

        return $this->roles
            ->flatMap(function ($role) {
                if (!$role->relationLoaded('permissions')) {
                    return collect();
                }
                return $role->permissions
                    ->pluck('name')
                    ->filter(fn (string $name) => str_starts_with($name, 'view-'));
            })
            ->unique()
            ->values()
            ->all();
    }

    /**
     * Treats the legacy `superAdmin` role as the super-admin flag.
     * The frontend uses it as a fast-path "show everything" check
     * without enumerating all 16 view-* keys.
     */
    private function isSuperAdmin(): bool
    {
        if (!$this->relationLoaded('roles')) {
            return false;
        }
        return $this->roles->contains(fn ($r) => in_array(
            strtolower((string) $r->name),
            ['superadmin', 'super-admin', 'super_admin'],
            true,
        ));
    }

    /**
     * Compact representation of the admin's primary role — what the
     * Controllers table renders as a colored badge.
     *
     *   { name, name_en, name_ar, color, is_system }
     *
     * `name` is the localised display label. Falls back to the raw
     * Spatie machine name when the additive bilingual columns aren't
     * populated (e.g. legacy roles created before the Roles redesign).
     *
     * Returns null when the admin has no roles assigned.
     *
     * @return array<string,mixed>|null
     */
    private function roleChip(string $locale): ?array
    {
        if (!$this->relationLoaded('roles')) return null;
        if ($this->roles->isEmpty())          return null;

        $role = $this->roles->first();

        $nameEn = $role->name_en ?? null;
        $nameAr = $role->name_ar ?? null;
        $display = $locale === 'ar'
            ? ($nameAr ?: ($nameEn ?: $role->name))
            : ($nameEn ?: ($nameAr ?: $role->name));

        return [
            'id'           => (int) $role->id,
            'machine_name' => (string) $role->name,
            'name'         => (string) $display,
            'name_en'      => $nameEn,
            'name_ar'      => $nameAr,
            'color'        => (string) ($role->color ?? 'teal'),
            'is_system'    => (bool) ($role->is_system ?? false),
        ];
    }
}
