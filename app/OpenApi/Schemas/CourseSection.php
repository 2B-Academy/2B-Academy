<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseSection",
 *     type="object",
 *     description="A course section / cohort. The Cohort tab on the course detail screen treats one section row as one cohort.",
 *     @OA\Property(property="id",             type="integer"),
 *     @OA\Property(property="course_id",      type="integer"),
 *     @OA\Property(property="name",           type="string", description="Localized display name."),
 *     @OA\Property(
 *         property="name_translations",
 *         type="object",
 *         description="Raw EN/AR pair so the edit dialog can pre-fill both inputs.",
 *         @OA\Property(property="en", type="string", nullable=true),
 *         @OA\Property(property="ar", type="string", nullable=true)
 *     ),
 *     @OA\Property(property="start_date",     type="string",  format="date", nullable=true),
 *     @OA\Property(property="end_date",       type="string",  format="date", nullable=true),
 *     @OA\Property(property="capacity",       type="integer", nullable=true, description="Max learners allowed in this cohort."),
 *     @OA\Property(property="status",         type="string",  enum={"scheduled","active","completed","inactive"}),
 *     @OA\Property(property="enrolled_count", type="integer", description="Live count of users enrolled into this cohort (users_courses.group_id)."),
 *     @OA\Property(property="order",          type="integer", nullable=true)
 * )
 */
class CourseSection {}
