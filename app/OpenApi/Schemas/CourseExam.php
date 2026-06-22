<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseExam",
 *     type="object",
 *     @OA\Property(property="id",        type="integer"),
 *     @OA\Property(property="course_id", type="integer"),
 *     @OA\Property(property="title",     type="string",  description="Localized title."),
 *     @OA\Property(property="degree",    type="integer", description="Total points."),
 *     @OA\Property(property="is_final",  type="boolean"),
 *     @OA\Property(
 *         property="questions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/CourseExamQuestion")
 *     )
 * )
 */
class CourseExam {}
