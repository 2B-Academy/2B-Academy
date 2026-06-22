<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Evaluation",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="category_id", type="integer"),
 *     @OA\Property(property="question",    type="string", description="Localized question text."),
 *     @OA\Property(property="type",        type="string", enum={"stars","text","yes_no"})
 * )
 */
class Evaluation {}
