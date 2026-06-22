<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseExamQuestion",
 *     type="object",
 *     @OA\Property(property="id",       type="integer"),
 *     @OA\Property(property="exam_id",  type="integer"),
 *     @OA\Property(property="question", type="string", description="Localized question text."),
 *     @OA\Property(
 *         property="answers",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",         type="integer"),
 *             @OA\Property(property="answer",     type="string"),
 *             @OA\Property(property="is_correct", type="boolean")
 *         )
 *     )
 * )
 */
class CourseExamQuestion {}
