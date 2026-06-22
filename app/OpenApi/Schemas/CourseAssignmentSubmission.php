<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseAssignmentSubmission",
 *     type="object",
 *     @OA\Property(property="id",            type="integer"),
 *     @OA\Property(property="assignment_id", type="integer"),
 *     @OA\Property(property="user_id",       type="integer"),
 *     @OA\Property(property="content",       type="string",  nullable=true),
 *     @OA\Property(property="file",          type="string",  format="uri", nullable=true),
 *     @OA\Property(property="score",         type="integer", nullable=true),
 *     @OA\Property(property="feedback",      type="string",  nullable=true),
 *     @OA\Property(property="submitted_at",  type="string",  format="date-time")
 * )
 */
class CourseAssignmentSubmission {}
