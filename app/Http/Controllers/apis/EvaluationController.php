<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\EvaluationRequest;
use App\Http\Resources\EvaluationResource;
use App\Models\Evaluation;
use App\Services\EvaluationCrudService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EvaluationController extends ApiController
{
    public function __construct(private readonly EvaluationCrudService $service) {}

    public function index(Request $request): JsonResponse
    {
        $evaluations = $this->service->paginate(
            perPage:    (int) $request->get('per_page', 20),
            search:     $request->get('search'),
            categoryId: $request->get('category_id') ? (int) $request->get('category_id') : null,
        );
        return $this->paginated(__('messages.retrieved'), $evaluations);
    }

    public function show(Evaluation $evaluation): JsonResponse
    {
        $evaluation->load('category');
        return $this->success(__('messages.retrieved'), new EvaluationResource($evaluation));
    }

    public function store(EvaluationRequest $request): JsonResponse
    {
        $evaluation = $this->service->create($request->validated());
        $evaluation->load('category');
        return $this->created(__('messages.created'), new EvaluationResource($evaluation));
    }

    public function update(Evaluation $evaluation, EvaluationRequest $request): JsonResponse
    {
        $updated = $this->service->update($evaluation, $request->validated());
        return $this->success(__('messages.updated'), new EvaluationResource($updated));
    }

    public function destroy(Evaluation $evaluation): JsonResponse
    {
        $this->service->delete($evaluation);
        return $this->deleted(__('messages.deleted'));
    }
}
