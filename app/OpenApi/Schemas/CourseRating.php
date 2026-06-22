<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseRating",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="course_id",  type="integer"),
 *     @OA\Property(property="user_id",    type="integer"),
 *     @OA\Property(property="stars",      type="integer", minimum=1, maximum=5),
 *     @OA\Property(property="comment",    type="string",  nullable=true),
 *     @OA\Property(property="created_at", type="string",  format="date-time")
 * )
 */
class CourseRating {}
