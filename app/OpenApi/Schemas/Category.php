<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Category",
 *     type="object",
 *     @OA\Property(property="id",            type="integer"),
 *     @OA\Property(property="name",          type="string", description="Localized name."),
 *     @OA\Property(property="logo",          type="string", format="uri", nullable=true),
 *     @OA\Property(property="active",        type="boolean"),
 *     @OA\Property(property="courses_count", type="integer"),
 *     @OA\Property(property="created_at",    type="string", format="date")
 * )
 */
class Category {}
