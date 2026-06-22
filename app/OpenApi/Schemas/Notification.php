<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Notification",
 *     type="object",
 *     @OA\Property(property="id",         type="integer"),
 *     @OA\Property(property="title",      type="string", description="Localized title."),
 *     @OA\Property(property="body",       type="string", description="Localized body."),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class Notification {}
