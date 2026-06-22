<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id",              type="integer", example=12),
 *     @OA\Property(property="name",            type="string",  example="Ahmed Ali"),
 *     @OA\Property(property="email",           type="string",  format="email", nullable=true),
 *     @OA\Property(property="phone",           type="string",  nullable=true),
 *     @OA\Property(property="system_id",       type="integer", nullable=true, description="HR system identifier"),
 *     @OA\Property(property="machine_code",    type="string",  nullable=true),
 *     @OA\Property(property="department_name", type="string",  nullable=true),
 *     @OA\Property(property="job_title",       type="string",  nullable=true),
 *     @OA\Property(property="roles",           type="array",   @OA\Items(type="string")),
 *     @OA\Property(property="created_at",      type="string",  format="date")
 * )
 */
class User {}
