<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="EnumOption",
 *     type="object",
 *     required={"id","value","code"},
 *     description="One dropdown option for a backend enum. `id` is a stable 1-indexed numeric identifier — the value the frontend POSTs/PUTs back. `value` is the localized display label rendered in the UI. `code` is the underlying string machine code (e.g. ""online""); use it when you need to interoperate with legacy string columns.",
 *     @OA\Property(property="id",          type="integer", example=1,        description="Stable numeric identifier (1-indexed). Sent on POST/PUT."),
 *     @OA\Property(property="value",       type="string",  example="Online", description="Localized display label for the active Accept-Language."),
 *     @OA\Property(property="code",        type="string",  example="online", description="Underlying string machine code (matches the DB column)."),
 *     @OA\Property(property="description", type="string",  nullable=true, example=null, description="Optional localized helper text (only set for enums that ship descriptions, e.g. certificate_basis).")
 * )
 *
 * @OA\Schema(
 *     schema="EnumOptionList",
 *     type="array",
 *     description="Ordered list of options for a single enum.",
 *     @OA\Items(ref="#/components/schemas/EnumOption")
 * )
 *
 * @OA\Schema(
 *     schema="EnumMap",
 *     type="object",
 *     description="Every enum keyed by its name. Returned by GET /api/v1/enums so the frontend can prime its dropdown cache in a single round-trip.",
 *     additionalProperties=@OA\AdditionalProperties(ref="#/components/schemas/EnumOptionList"),
 *     example={
 *         "course_type": {
 *             {"id": 1, "value": "Online",        "code": "online"},
 *             {"id": 2, "value": "Offline",       "code": "offline"},
 *             {"id": 3, "value": "Hybrid",        "code": "hybrid"},
 *             {"id": 4, "value": "External Link", "code": "external_link"}
 *         },
 *         "cohort_status": {
 *             {"id": 1, "value": "Scheduled", "code": "scheduled"},
 *             {"id": 2, "value": "Active",    "code": "active"},
 *             {"id": 3, "value": "Completed", "code": "completed"},
 *             {"id": 4, "value": "Inactive",  "code": "inactive"}
 *         }
 *     }
 * )
 */
class EnumOption
{
}
