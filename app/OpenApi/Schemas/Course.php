<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Course",
 *     type="object",
 *     @OA\Property(property="id",                  type="integer"),
 *     @OA\Property(property="title",               type="string", description="Localized course title."),
 *     @OA\Property(property="description",         type="string", description="Localized course description."),
 *     @OA\Property(property="course_type",         type="string", enum={"online","offline"}),
 *     @OA\Property(
 *         property="category",
 *         type="object",
 *         @OA\Property(property="id",   type="integer"),
 *         @OA\Property(property="name", type="string")
 *     ),
 *     @OA\Property(
 *         property="instructors",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",   type="integer"),
 *             @OA\Property(property="name", type="string")
 *         )
 *     ),
 *     @OA\Property(
 *         property="qualification_skills",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",   type="integer"),
 *             @OA\Property(property="name", type="string")
 *         )
 *     ),
 *     @OA\Property(property="image",             type="string",  format="uri", nullable=true),
 *     @OA\Property(property="intro_video",       type="string",  nullable=true),
 *     @OA\Property(property="hours",             type="integer"),
 *     @OA\Property(property="language",          type="string",  nullable=true),
 *     @OA\Property(property="level",             type="string",  nullable=true),
 *     @OA\Property(property="price",             type="number",  format="float", nullable=true),
 *     @OA\Property(property="currency",          type="string",  nullable=true),
 *     @OA\Property(property="certificate",       type="boolean"),
 *     @OA\Property(property="active",            type="boolean"),
 *     @OA\Property(property="for_public",        type="boolean"),
 *     @OA\Property(property="is_evaluate",       type="boolean"),
 *     @OA\Property(property="outside_materials", type="boolean"),
 *     @OA\Property(property="allow_attendances", type="boolean"),
 *     @OA\Property(property="created_at",        type="string", format="date")
 * )
 */
class Course {}
