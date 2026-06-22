<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Certificate",
 *     type="object",
 *     @OA\Property(property="id",            type="integer"),
 *     @OA\Property(property="user_id",       type="integer"),
 *     @OA\Property(property="course_id",     type="integer"),
 *     @OA\Property(property="course_title",  type="string"),
 *     @OA\Property(property="issued_at",     type="string", format="date"),
 *     @OA\Property(property="download_url",  type="string", format="uri", nullable=true)
 * )
 */
class Certificate {}
