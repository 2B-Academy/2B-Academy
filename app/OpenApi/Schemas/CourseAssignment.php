<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseAssignment",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="course_id",   type="integer"),
 *     @OA\Property(property="title",       type="string",  description="Localized title."),
 *     @OA\Property(property="description", type="string",  description="Localized description."),
 *     @OA\Property(property="due_date",    type="string",  format="date-time", nullable=true)
 * )
 */
class CourseAssignment {}
