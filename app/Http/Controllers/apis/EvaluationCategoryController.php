<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\EvaluationCategoryRequest;
use App\Http\Resources\EvaluationCategoryResource;
use App\Models\EvaluationCategory;
use App\Services\EvaluationCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EvaluationCategoryController extends ApiController
{
    public function __construct(private readonly EvaluationCategoryService $service) {}

    /**
     * @OA\Get(
     *     path="/evaluation-categories",
     *     tags={"Evaluation Categories"},
     *     summary="List evaluation categories (paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated evaluation categories",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/EvaluationCategory")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $categories);
    }

    /**
     * @OA\Get(
     *     path="/evaluation-categories/all",
     *     tags={"Evaluation Categories"},
     *     summary="List ALL evaluation categories (no pagination). For select dropdowns.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="All evaluation categories",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/EvaluationCategory")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function all(): JsonResponse
    {
        return $this->success(__('messages.retrieved'),
            EvaluationCategoryResource::collection($this->service->all())
        );
    }

    /**
     * @OA\Get(
     *     path="/evaluation-categories/{evaluationCategory}",
     *     tags={"Evaluation Categories"},
     *     summary="Show an evaluation category.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="evaluationCategory", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Evaluation category detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EvaluationCategory"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(EvaluationCategory $evaluationCategory): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new EvaluationCategoryResource($evaluationCategory));
    }

    /**
     * @OA\Post(
     *     path="/evaluation-categories",
     *     tags={"Evaluation Categories"},
     *     summary="Create an evaluation category.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", ref="#/components/schemas/TranslatedString")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EvaluationCategory"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(EvaluationCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new EvaluationCategoryResource($category));
    }

    /**
     * @OA\Put(
     *     path="/evaluation-categories/{evaluationCategory}",
     *     tags={"Evaluation Categories"},
     *     summary="Update an evaluation category.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="evaluationCategory", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", ref="#/components/schemas/TranslatedString")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/EvaluationCategory"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(EvaluationCategory $evaluationCategory, EvaluationCategoryRequest $request): JsonResponse
    {
        $updated = $this->service->update($evaluationCategory, $request->validated());
        return $this->success(__('messages.updated'), new EvaluationCategoryResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/evaluation-categories/{evaluationCategory}",
     *     tags={"Evaluation Categories"},
     *     summary="Delete an evaluation category.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="evaluationCategory", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(EvaluationCategory $evaluationCategory): JsonResponse
    {
        $this->service->delete($evaluationCategory);
        return $this->deleted(__('messages.deleted'));
    }
}
