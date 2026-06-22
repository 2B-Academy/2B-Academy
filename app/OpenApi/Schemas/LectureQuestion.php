<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="LectureQuestion",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="lecture_id", type="integer"),
 *     @OA\Property(property="user_id",    type="integer"),
 *     @OA\Property(property="question",   type="string"),
 *     @OA\Property(property="answer",     type="string", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class LectureQuestion {}
