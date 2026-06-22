<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="CohortAttendance",
 *     type="object",
 *     @OA\Property(
 *         property="cohort",
 *         type="object",
 *         @OA\Property(property="id",         type="integer"),
 *         @OA\Property(property="name",       type="string"),
 *         @OA\Property(property="start_date", type="string", format="date", nullable=true),
 *         @OA\Property(property="end_date",   type="string", format="date", nullable=true)
 *     ),
 *     @OA\Property(
 *         property="course",
 *         type="object",
 *         @OA\Property(property="id",    type="integer"),
 *         @OA\Property(property="title", type="string")
 *     ),
 *     @OA\Property(
 *         property="totals",
 *         type="object",
 *         @OA\Property(property="sessions", type="integer"),
 *         @OA\Property(property="learners", type="integer"),
 *         @OA\Property(property="attended", type="integer"),
 *         @OA\Property(property="absent",   type="integer")
 *     ),
 *     @OA\Property(
 *         property="sessions",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",              type="integer"),
 *             @OA\Property(property="index",           type="integer", description="1-based position in the chronological list."),
 *             @OA\Property(property="title",           type="string"),
 *             @OA\Property(property="date",            type="string", format="date",  nullable=true),
 *             @OA\Property(property="time_from",      type="string", nullable=true),
 *             @OA\Property(property="time_to",         type="string", nullable=true),
 *             @OA\Property(property="location",        type="string", nullable=true),
 *             @OA\Property(property="attended_count",  type="integer"),
 *             @OA\Property(property="absent_count",    type="integer"),
 *             @OA\Property(property="total",           type="integer"),
 *             @OA\Property(property="full_attendance", type="boolean"),
 *             @OA\Property(
 *                 property="absent_learners",
 *                 type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id",   type="integer"),
 *                     @OA\Property(property="name", type="string")
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Property(
 *         property="learners",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",              type="integer"),
 *             @OA\Property(property="name",            type="string"),
 *             @OA\Property(property="machine_code",    type="string", nullable=true),
 *             @OA\Property(property="department",      type="string", nullable=true),
 *             @OA\Property(property="total_sessions",  type="integer"),
 *             @OA\Property(property="attended_count",  type="integer"),
 *             @OA\Property(property="absent_count",    type="integer"),
 *             @OA\Property(
 *                 property="absent_sessions",
 *                 type="array",
 *                 @OA\Items(type="object",
 *                     @OA\Property(property="id",    type="integer"),
 *                     @OA\Property(property="index", type="integer"),
 *                     @OA\Property(property="title", type="string"),
 *                     @OA\Property(property="date",  type="string", format="date", nullable=true)
 *                 )
 *             )
 *         )
 *     )
 * )
 */
class CohortAttendance {}
