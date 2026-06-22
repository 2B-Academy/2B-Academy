<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Testimonial",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="name",        type="string"),
 *     @OA\Property(property="title",       type="string", description="Localized title.", nullable=true),
 *     @OA\Property(property="description", type="string", description="Localized description."),
 *     @OA\Property(property="image",       type="string", format="uri", nullable=true),
 *     @OA\Property(property="active",      type="boolean")
 * )
 */
class Testimonial {}
