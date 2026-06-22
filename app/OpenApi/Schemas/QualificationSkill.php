<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="QualificationSkill",
 *     type="object",
 *     @OA\Property(property="id",            type="integer", example=1),
 *     @OA\Property(property="name",          type="string",  example="Communication", description="Localized name (Arabic or English depending on request locale)."),
 *     @OA\Property(property="courses_count", type="integer", example=3),
 *     @OA\Property(property="created_at",    type="string",  format="date")
 * )
 */
class QualificationSkill {}
