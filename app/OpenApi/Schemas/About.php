<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="About",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="title",       type="string", description="Localized title."),
 *     @OA\Property(property="description", type="string", description="Localized description."),
 *     @OA\Property(property="vision",      type="string", description="Localized vision.", nullable=true),
 *     @OA\Property(property="mission",     type="string", description="Localized mission.", nullable=true)
 * )
 */
class About {}
