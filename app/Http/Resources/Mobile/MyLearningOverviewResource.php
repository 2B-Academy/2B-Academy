<?php

declare(strict_types=1);

namespace App\Http\Resources\Mobile;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * S-05 "Profile — My Learning" overview composite. Carries:
 *
 *   - learner identity block (machine_code, name, department, job_title)
 *   - active course previews (top N by recency)
 *   - qualification progress previews (top N by % done)
 *   - certificate previews (top N by issued date)
 *   - counts so the "View all" link can show the right number
 *
 * Everything that's a "count" comes from the same repository the
 * pageable list endpoint uses, so the overview stays in lock-step
 * with what tapping "View all" would return.
 *
 * The `learner` block is the canonical identity card at the top of
 * the screen — `machine_code` is the HR-sourced employee code that
 * the mobile UI surfaces under the user's name.
 */
class MyLearningOverviewResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $row = $this->resource;

        return [
            'learner' => $request->user()
                ? (new LearnerIdentityResource($request->user()))->toArray($request)
                : null,
            'counts' => [
                'active_courses'   => (int) $row['counts']['active_courses'],
                'qualifications'   => (int) $row['counts']['qualifications'],
                'certificates'     => (int) $row['counts']['certificates'],
            ],
            'previews' => [
                'active_courses' => MyLearningActiveCourseResource::collection($row['previews']['active_courses']),
                'qualifications' => QualificationProgressResource::collection($row['previews']['qualifications']),
                'certificates'   => CertificateResource::collection($row['previews']['certificates']),
            ],
        ];
    }
}
