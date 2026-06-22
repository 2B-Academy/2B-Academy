<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="UserExam",
 *     type="object",
 *     @OA\Property(property="id",           type="integer"),
 *     @OA\Property(property="user_id",      type="integer"),
 *     @OA\Property(property="course_id",    type="integer"),
 *     @OA\Property(property="exam_id",      type="integer"),
 *     @OA\Property(property="score",        type="integer"),
 *     @OA\Property(property="max_score",    type="integer"),
 *     @OA\Property(property="passed",       type="boolean"),
 *     @OA\Property(property="submitted_at", type="string",  format="date-time")
 * )
 */
class UserExam {}
