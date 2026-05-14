<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends ApiController
{
    public function __construct(private readonly CategoryService $categoryService) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
            active:  $request->has('active') ? filter_var($request->active, FILTER_VALIDATE_BOOLEAN) : null,
        );

        return $this->paginated(__('messages.retrieved'), CategoryResource::collection($categories));
    }

    public function activeList(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            CategoryResource::collection($this->categoryService->allActive()),
        );
    }

    public function show(Category $category): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new CategoryResource($category->loadCount('courses')),
        );
    }

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

    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->delete($category);
        return $this->deleted();
    }
}
