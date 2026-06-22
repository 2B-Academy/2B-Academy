<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseSession",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="course_id",  type="integer"),
 *     @OA\Property(property="title",      type="string",  description="Localized session title."),
 *     @OA\Property(property="start_date", type="string",  format="date-time"),
 *     @OA\Property(property="end_date",   type="string",  format="date-time", nullable=true),
 *     @OA\Property(property="location",   type="string",  nullable=true)
 * )
 */
class CourseSession {}
