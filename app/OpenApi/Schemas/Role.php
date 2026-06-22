<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="name",        type="string"),
 *     @OA\Property(property="guard_name",  type="string"),
 *     @OA\Property(property="permissions", type="array", @OA\Items(type="string"))
 * )
 */
class Role {}
