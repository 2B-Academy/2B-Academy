<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CourseLecture",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="section_id", type="integer"),
 *     @OA\Property(property="title",      type="string",  description="Localized title."),
 *     @OA\Property(property="type",       type="string",  enum={"url","file","video"}),
 *     @OA\Property(property="video_url",  type="string",  nullable=true),
 *     @OA\Property(property="order",      type="integer", nullable=true)
 * )
 */
class CourseLecture {}
