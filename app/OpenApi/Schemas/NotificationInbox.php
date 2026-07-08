<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="NotificationInbox",
 *     type="object",
 *     @OA\Property(property="id",         type="string", description="UUID of the notification."),
 *     @OA\Property(property="type",       type="string", description="Event slug, e.g. pending_grade, rating_dropped."),
 *     @OA\Property(property="title",      type="string", description="Localized title."),
 *     @OA\Property(property="body",       type="string", description="Localized body."),
 *     @OA\Property(property="meta",       type="object", description="Deep-linking ids (course_id, etc.)."),
 *     @OA\Property(property="read_at",    type="string", format="date-time", nullable=true),
 *     @OA\Property(property="created_at", type="string", format="date-time")
 * )
 */
class NotificationInbox {}
