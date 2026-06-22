<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class ArticleController extends ApiController
{
    public function __construct(private readonly ArticleService $service) {}

    /**
     * @OA\Get(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="List articles (paginated). Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         required=false,
     *         description="Filter by article type.",
     *         @OA\Schema(type="string", enum={"news","blogs","event"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated articles",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Article")
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $articles = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            type:    $request->get('type'),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $articles);
    }

    /**
     * @OA\Get(
     *     path="/articles/{article}",
     *     tags={"Articles"},
     *     summary="Show an article. Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="article", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Article",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Article"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Article $article): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new ArticleResource($article));
    }

    /**
     * @OA\Post(
     *     path="/articles",
     *     tags={"Articles"},
     *     summary="Create an article (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"type","title","description","slug","image"},
     *                 @OA\Property(property="type",         type="string", enum={"news","blogs","event"}),
     *                 @OA\Property(property="title",        ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="description",  ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="slug",         type="string", maxLength=255),
     *                 @OA\Property(property="date_publish", type="string", format="date", nullable=true),
     *                 @OA\Property(property="image",        type="string", format="binary", description="PNG/JPG/JPEG/WEBP/SVG/GIF, max 2MB."),
     *                 @OA\Property(property="is_home",      type="boolean", nullable=true),
     *                 @OA\Property(property="active",       type="boolean", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Article"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(ArticleRequest $request): JsonResponse
    {
        $article = $this->service->create($request->validated(), $request->file('image'));
        return $this->created(__('messages.created'), new ArticleResource($article));
    }

    /**
     * @OA\Put(
     *     path="/articles/{article}",
     *     tags={"Articles"},
     *     summary="Update an article (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="article", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="type",         type="string", enum={"news","blogs","event"}),
     *                 @OA\Property(property="title",        ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="description",  ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="slug",         type="string", maxLength=255),
     *                 @OA\Property(property="date_publish", type="string", format="date", nullable=true),
     *                 @OA\Property(property="image",        type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP/SVG/GIF, max 2MB."),
     *                 @OA\Property(property="is_home",      type="boolean", nullable=true),
     *                 @OA\Property(property="active",       type="boolean", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Article"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Article $article, ArticleRequest $request): JsonResponse
    {
        $updated = $this->service->update($article, $request->validated(), $request->file('image'));
        return $this->success(__('messages.updated'), new ArticleResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/articles/{article}",
     *     tags={"Articles"},
     *     summary="Delete an article (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="article", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Article $article): JsonResponse
    {
        $this->service->delete($article);
        return $this->deleted(__('messages.deleted'));
    }
}
