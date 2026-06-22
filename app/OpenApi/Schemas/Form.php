<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="Form",
 *     type="object",
 *     @OA\Property(property="id",          type="integer"),
 *     @OA\Property(property="uuid",        type="string", format="uuid"),
 *     @OA\Property(property="title",       type="string", description="Localized form title."),
 *     @OA\Property(property="description", type="string", description="Localized description.", nullable=true),
 *     @OA\Property(property="active",      type="boolean"),
 *     @OA\Property(
 *         property="questions",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/FormQuestion")
 *     )
 * )
 */
class Form {}
