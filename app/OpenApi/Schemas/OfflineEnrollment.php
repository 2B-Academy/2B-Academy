<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OfflineEnrollment",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="user_id",    type="integer"),
 *     @OA\Property(property="course_id",  type="integer"),
 *     @OA\Property(property="group_id",   type="integer", nullable=true),
 *     @OA\Property(property="created_at", type="string",  format="date-time")
 * )
 */
class OfflineEnrollment {}
