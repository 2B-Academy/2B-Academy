<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseDetail",
 *     allOf={
 *         @OA\Schema(ref="#/components/schemas/Course"),
 *         @OA\Schema(
 *             type="object",
 *             @OA\Property(property="title_for_certificate", type="string", nullable=true),
 *             @OA\Property(property="sections", type="array", @OA\Items(ref="#/components/schemas/CourseSection")),
 *             @OA\Property(
 *                 property="exams",
 *                 type="array",
 *                 @OA\Items(
 *                     type="object",
 *                     @OA\Property(property="id",       type="integer"),
 *                     @OA\Property(property="title",    type="string"),
 *                     @OA\Property(property="degree",   type="integer"),
 *                     @OA\Property(property="is_final", type="boolean")
 *                 )
 *             )
 *         )
 *     }
 * )
 */
class CourseDetail {}
