<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Cohort Attendance — thin pass-through.
 *
 * `CohortAttendanceService::build()` already produces the exact wire
 * shape the frontend drawer expects (see the Figma designs referenced
 * in senior-full-stack.md → "Cohort Attendance"). The resource layer
 * exists for two reasons:
 *
 *   1. Consistency with every other API endpoint in this codebase —
 *      controllers always hand the response off to a Resource so the
 *      JSON shape lives in one well-known place.
 *
 *   2. A single hook to evolve the shape later (e.g. wrapping dates
 *      with `->toDateString()` or renaming a key) without touching
 *      the service that does the actual aggregation.
 */
class CohortAttendanceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return (array) $this->resource;
    }
}
