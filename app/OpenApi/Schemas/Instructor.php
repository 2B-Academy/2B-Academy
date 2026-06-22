<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Instructor",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="name",       type="string", description="Localized name."),
 *     @OA\Property(property="bio",        type="string", description="Localized bio.", nullable=true),
 *     @OA\Property(property="job_title",  type="string", description="Localized job title.", nullable=true),
 *     @OA\Property(property="image",      type="string", format="uri", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date")
 * )
 */
class Instructor {}
