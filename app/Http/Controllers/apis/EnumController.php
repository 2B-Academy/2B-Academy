<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis;

use App\Enums\EnumRegistry;
use App\Http\Resources\EnumOptionResource;
use App\Services\EnumService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

/**
 * Public dropdown reference data for the entire frontend.
 *
 * The Angular app calls these endpoints on bootstrap (and again whenever the
 * active locale changes) instead of hardcoding option arrays. Every option is
 * shaped `{id, value, description?}` where `id` is the canonical machine
 * code (POSTed back to the API) and `value` is the localized display label
 * resolved against the active `Accept-Language` header.
 */
class EnumController extends ApiController
{
    public function __construct(private readonly EnumService $enums) {}

    /**
     * @OA\Get(
     *     path="/enums",
     *     tags={"Enums"},
     *     summary="Bulk: every enum at once (localized).",
     *     description="Optional convenience endpoint. Returns the full enum map so the frontend can prime its dropdown cache in a single round-trip. Honors `Accept-Language: en|ar`. The keys of `result` are the enum names; each value is the ordered list of `{id, value, description?}` options for that enum.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Map of every registered enum.",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     ref="#/components/schemas/EnumMap"
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), $this->enums->all());
    }

    /**
     * Generic per-enum handler. Each enum gets its own Swagger entry via the
     * dedicated `@OA\Get` annotations in {@see \App\OpenApi\EnumEndpoints},
     * but the actual HTTP routing all lands here.
     */
    public function show(string $name): JsonResponse
    {
        if (! EnumRegistry::exists($name)) {
            return $this->notFound(__('messages.not_found'));
        }

        return $this->success(
            __('messages.retrieved'),
            EnumOptionResource::collection($this->enums->options($name)),
        );
    }
}
