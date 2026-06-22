<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="EvaluationCategory",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="title",       type="string", description="Localized title."),
 *     @OA\Property(property="description", type="string", description="Localized description.", nullable=true)
 * )
 */
class EvaluationCategory {}
