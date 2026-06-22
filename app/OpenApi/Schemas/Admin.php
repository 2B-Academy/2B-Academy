<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Admin",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="name",       type="string"),
 *     @OA\Property(property="email",      type="string", format="email"),
 *     @OA\Property(property="roles",      type="array",  @OA\Items(type="string")),
 *     @OA\Property(property="created_at", type="string", format="date")
 * )
 */
class Admin {}
