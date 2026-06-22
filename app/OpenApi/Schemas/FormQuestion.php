<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="FormQuestion",
 *     type="object",
 *     @OA\Property(property="id",       type="integer"),
 *     @OA\Property(property="form_id",  type="integer"),
 *     @OA\Property(property="question", type="string", description="Localized question."),
 *     @OA\Property(property="type",     type="string", enum={"text","radio","checkbox","stars","yes_no"}),
 *     @OA\Property(
 *         property="answers",
 *         type="array",
 *         @OA\Items(
 *             type="object",
 *             @OA\Property(property="id",     type="integer"),
 *             @OA\Property(property="answer", type="string")
 *         )
 *     )
 * )
 */
class FormQuestion {}
