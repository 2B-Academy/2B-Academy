<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\EvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Services\EvaluationCrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class EvaluationController extends ApiController
{
    public function __construct(private readonly EvaluationCrudService $service) {}

    /**
     * @OA\Get(
     *     path="/evaluations",
     *     tags={"Evaluations"},
     *     summary="List evaluation questions (paginated, optionally filtered by category).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         required=false,
     *         description="Filter by evaluation category id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated evaluations",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Evaluation")
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
        $evaluations = $this->service->paginate(
            perPage:    (int) $request->get('per_page', 20),
            search:     $request->get('search'),
            categoryId: $request->get('category_id') ? (int) $request->get('category_id') : null,
        );
        return $this->paginated(__('messages.retrieved'), $evaluations);
    }

    /**
     * @OA\Get(
     *     path="/evaluations/{evaluation}",
     *     tags={"Evaluations"},
     *     summary="Show an evaluation question (with category).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="evaluation", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Evaluation detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Evaluation"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Evaluation $evaluation): JsonResponse
    {
        $evaluation = $this->service->findWithCategory($evaluation->id);
        return $this->success(__('messages.retrieved'), new EvaluationResource($evaluation));
    }

    /**
     * @OA\Post(
     *     path="/evaluations",
     *     tags={"Evaluations"},
     *     summary="Create an evaluation question.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"evaluation_category_id","type","title"},
     *             @OA\Property(property="evaluation_category_id", type="integer", description="Existing evaluation category id."),
     *             @OA\Property(property="type",                   type="string",  enum={"text","five","ten"}),
     *             @OA\Property(property="title",                  ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="is_required",            type="boolean", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Evaluation"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(EvaluationRequest $request): JsonResponse
    {
        $evaluation = $this->service->create($request->validated());
        $evaluation = $this->service->findWithCategory($evaluation->id);
        return $this->created(__('messages.created'), new EvaluationResource($evaluation));
    }

    /**
     * @OA\Put(
     *     path="/evaluations/{evaluation}",
     *     tags={"Evaluations"},
     *     summary="Update an evaluation question.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="evaluation", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"evaluation_category_id","type","title"},
     *             @OA\Property(property="evaluation_category_id", type="integer"),
     *             @OA\Property(property="type",                   type="string", enum={"text","five","ten"}),
     *             @OA\Property(property="title",                  ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="is_required",            type="boolean", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Evaluation"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Evaluation $evaluation, EvaluationRequest $request): JsonResponse
    {
        $updated = $this->service->update($evaluation, $request->validated());
        return $this->success(__('messages.updated'), new EvaluationResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/evaluations/{evaluation}",
     *     tags={"Evaluations"},
     *     summary="Delete an evaluation question.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="evaluation", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Evaluation $evaluation): JsonResponse
    {
        $this->service->delete($evaluation);
        return $this->deleted(__('messages.deleted'));
    }
}
