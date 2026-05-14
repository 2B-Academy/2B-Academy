<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\EvaluationCategoryRequest;
use App\Http\Resources\EvaluationCategoryResource;
use App\Models\EvaluationCategory;
use App\Services\EvaluationCategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationCategoryController extends ApiController
{
    public function __construct(private readonly EvaluationCategoryService $service) {}

    public function index(Request $request): JsonResponse
    {
        $categories = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $categories);
    }

    public function all(): JsonResponse
    {
        return $this->success(__('messages.retrieved'),
            EvaluationCategoryResource::collection($this->service->all())
        );
    }

    public function show(EvaluationCategory $evaluationCategory): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new EvaluationCategoryResource($evaluationCategory));
    }

    public function store(EvaluationCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());
        return $this->created(__('messages.created'), new EvaluationCategoryResource($category));
    }

    public function update(EvaluationCategory $evaluationCategory, EvaluationCategoryRequest $request): JsonResponse
    {
        $updated = $this->service->update($evaluationCategory, $request->validated());
        return $this->success(__('messages.updated'), new EvaluationCategoryResource($updated));
    }

    public function destroy(EvaluationCategory $evaluationCategory): JsonResponse
    {
        $this->service->delete($evaluationCategory);
        return $this->deleted(__('messages.deleted'));
    }
}
