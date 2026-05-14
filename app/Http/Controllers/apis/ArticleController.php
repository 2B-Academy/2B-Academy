<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\ArticleRequest;
use App\Http\Resources\ArticleResource;
use App\Models\Article;
use App\Services\ArticleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArticleController extends ApiController
{
    public function __construct(private readonly ArticleService $service) {}

    public function index(Request $request): JsonResponse
    {
        $articles = $this->service->paginate(
            perPage: (int) $request->get('per_page', 20),
            type:    $request->get('type'),
            search:  $request->get('search'),
        );
        return $this->paginated(__('messages.retrieved'), $articles);
    }

    public function show(Article $article): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new ArticleResource($article));
    }

    public function store(ArticleRequest $request): JsonResponse
    {
        $article = $this->service->create($request->validated(), $request->file('image'));
        return $this->created(__('messages.created'), new ArticleResource($article));
    }

    public function update(Article $article, ArticleRequest $request): JsonResponse
    {
        $updated = $this->service->update($article, $request->validated(), $request->file('image'));
        return $this->success(__('messages.updated'), new ArticleResource($updated));
    }

    public function destroy(Article $article): JsonResponse
    {
        $this->service->delete($article);
        return $this->deleted(__('messages.deleted'));
    }
}
