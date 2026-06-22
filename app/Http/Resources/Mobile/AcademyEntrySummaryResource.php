<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-01 Academy entry card. `count` is the number of *available* courses
 * for THIS user (i.e. courses with at least one open cohort the user
 * hasn't joined yet). Computed by AcademyService::summaryFor.
 *
 * Includes the `learner` identity block (with `machine_code`) so the
 * home screen card can display the learner's HR code next to the
 * available-courses count.
 *
 * @property-read int  $available_count
 * @property-read bool $has_available
 */
class AcademyEntrySummaryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'learner' => $request->user()
                ? (new LearnerIdentityResource($request->user()))->toArray($request)
                : null,
            'available_count' => (int) $this->resource['available_count'],
            'has_available'   => (bool) $this->resource['has_available'],
        ];
    }
}
