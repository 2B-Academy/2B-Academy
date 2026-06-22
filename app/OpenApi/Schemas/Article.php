<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Article",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="title",       type="string", description="Localized title."),
 *     @OA\Property(property="description", type="string", description="Localized description."),
 *     @OA\Property(property="image",       type="string", format="uri", nullable=true),
 *     @OA\Property(property="created_at",  type="string", format="date")
 * )
 */
class Article {}
