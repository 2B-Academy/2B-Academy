<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="OnlineEnrollment",
 *     type="object",
 *     @OA\Property(property="user_id",     type="integer"),
 *     @OA\Property(property="course_id",   type="integer"),
 *     @OA\Property(property="enrolled_at", type="string", format="date-time")
 * )
 */
class OnlineEnrollment {}
