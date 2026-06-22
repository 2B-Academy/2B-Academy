<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CategoryController extends ApiController
{
    public function __construct(private readonly CategoryService $categoryService) {}

    /**
     * @OA\Get(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="List categories (paginated, admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="active",
     *         in="query",
     *         required=false,
     *         description="Filter by active flag.",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated categories",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Category")
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
        $categories = $this->categoryService->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
            active:  $request->has('active') ? filter_var($request->active, FILTER_VALIDATE_BOOLEAN) : null,
        );

        return $this->paginated(__('messages.retrieved'), CategoryResource::collection($categories));
    }

    /**
     * @OA\Get(
     *     path="/categories/active",
     *     tags={"Categories"},
     *     summary="List active categories (no pagination). Public — for frontend dropdowns.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Active categories",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Category")
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function activeList(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            CategoryResource::collection($this->categoryService->allActive()),
        );
    }

    /**
     * @OA\Get(
     *     path="/categories/{category}",
     *     tags={"Categories"},
     *     summary="Show a category (with courses_count).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Category",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Category"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Category $category): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new CategoryResource($category->loadCount('courses')),
        );
    }

    /**
     * @OA\Post(
     *     path="/categories",
     *     tags={"Categories"},
     *     summary="Create a category (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","logo"},
     *                 @OA\Property(property="name",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="active", type="boolean", nullable=true),
     *                 @OA\Property(property="logo",   type="string", format="binary", description="PNG/JPG/JPEG/WEBP/SVG, max 2MB.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Category"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(CategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create(
            $request->validated(),
            $request->file('logo'),
        );

        return $this->created(
            __('messages.created'),
            new CategoryResource($category),
        );
    }

    /**
     * @OA\Put(
     *     path="/categories/{category}",
     *     tags={"Categories"},
     *     summary="Update a category (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="active", type="boolean", nullable=true),
     *                 @OA\Property(property="logo",   type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP/SVG, max 2MB.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Category"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $category = $this->categoryService->update(
            $category,
            $request->validated(),
            $request->file('logo'),
        );

        return $this->success(
            __('messages.updated'),
            new CategoryResource($category),
        );
    }

    /**
     * @OA\Delete(
     *     path="/categories/{category}",
     *     tags={"Categories"},
     *     summary="Delete a category (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="category", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);
        return $this->deleted();
    }
}
