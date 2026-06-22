<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="SessionPasscodeState",
 *     type="object",
 *     description="Instructor-dashboard passcode widget state (Figma 515:34995 / 515:37969 / 515:35489).",
 *     @OA\Property(property="available", type="boolean", description="True only when the viewer is an instructor who teaches courses. When false the widget is hidden."),
 *     @OA\Property(property="state", type="string", enum={"idle","live","ended"}, description="idle = show Generate Passcode; live = show digits; ended = show digits + Session ended."),
 *     @OA\Property(property="passcode_length", type="integer", example=5),
 *     @OA\Property(
 *         property="session",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="id",           type="integer"),
 *         @OA\Property(property="number",       type="integer", description="1-based ordinal of the session within its cohort."),
 *         @OA\Property(property="title",        type="string",  nullable=true),
 *         @OA\Property(property="date",         type="string",  format="date", nullable=true),
 *         @OA\Property(property="time_from",    type="string",  example="10:00", nullable=true),
 *         @OA\Property(property="time_to",      type="string",  example="10:30", nullable=true),
 *         @OA\Property(property="course_id",    type="integer"),
 *         @OA\Property(property="course_title", type="string",  nullable=true),
 *         @OA\Property(property="cohort_id",    type="integer", nullable=true),
 *         @OA\Property(property="cohort_name",  type="string",  nullable=true)
 *     ),
 *     @OA\Property(
 *         property="passcode",
 *         type="object",
 *         nullable=true,
 *         @OA\Property(property="code",       type="string",  example="33333"),
 *         @OA\Property(property="issued_at",  type="string",  format="date-time", nullable=true),
 *         @OA\Property(property="expires_at", type="string",  format="date-time", nullable=true),
 *         @OA\Property(property="expired",    type="boolean")
 *     )
 * )
 */
class SessionPasscodeState {}
