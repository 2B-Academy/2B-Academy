<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Detail-view resource backing the "User Profile" right-side drawer in the
 * 2026 Figma redesign. Returns the same shape as AdminUserListResource —
 * the drawer surfaces the same data plus an enrolled-courses count for
 * learner rows.
 */
class AdminUserDetailResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return (new AdminUserListResource($this->resource))->toArray($request);
    }
}
