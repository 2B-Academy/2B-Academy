<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="LectureProgress",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="lecture_id", type="integer"),
 *     @OA\Property(property="user_id",    type="integer"),
 *     @OA\Property(property="watched",    type="boolean"),
 *     @OA\Property(property="completed",  type="boolean"),
 *     @OA\Property(property="updated_at", type="string", format="date-time")
 * )
 */
class LectureProgress {}
