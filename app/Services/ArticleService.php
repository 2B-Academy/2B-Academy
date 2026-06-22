<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    use HasFile;

    public function __construct(
        private readonly ArticleRepositoryInterface $repo
    ) {}

    public function paginate(int $perPage = 20, ?string $type = null, ?string $search = null): LengthAwarePaginator
    {
        return $this->repo->paginateWithFilters($perPage, $type, $search);
    }

    public function find(int $id): Article
    {
        /** @var Article */
        return $this->repo->findOrFail($id);
    }

    public function create(array $data, $imageFile = null): Article
    {
        if ($imageFile) {
            $data['image'] = $this->uploadRequestFile('Article', request(), null, $imageFile);
        }
        $data['is_home'] = (bool) ($data['is_home'] ?? false);
        $data['active']  = (bool) ($data['active'] ?? true);

        /** @var Article */
        return $this->repo->create($data);
    }

    public function update(Article $article, array $data, $imageFile = null): Article
    {
        if ($imageFile) {
            $data['image'] = $this->uploadRequestFile('Article', request(), null, $imageFile);
        }
        $data['is_home'] = (bool) ($data['is_home'] ?? false);
        $data['active']  = (bool) ($data['active'] ?? $article->active);

        /** @var Article */
        return $this->repo->update($article, $data);
    }

    public function delete(Article $article): void
    {
        $this->repo->delete($article);
    }
}
