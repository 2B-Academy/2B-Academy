<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Article;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleService
{
    use HasFile;

    public function paginate(int $perPage = 20, ?string $type = null, ?string $search = null): LengthAwarePaginator
    {
        return Article::when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->where('title->ar', 'like', "%$search%")
                ->orWhere('title->en', 'like', "%$search%")
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function find(int $id): Article
    {
        return Article::findOrFail($id);
    }

    public function create(array $data, $imageFile = null): Article
    {
        if ($imageFile) {
            $data['image'] = $this->uploadFile('Article', $imageFile);
        }
        $data['is_home'] = (bool) ($data['is_home'] ?? false);
        $data['active']  = (bool) ($data['active'] ?? true);
        return Article::create($data);
    }

    public function update(Article $article, array $data, $imageFile = null): Article
    {
        if ($imageFile) {
            $data['image'] = $this->uploadFile('Article', $imageFile);
        }
        $data['is_home'] = (bool) ($data['is_home'] ?? false);
        $data['active']  = (bool) ($data['active'] ?? $article->active);
        $article->update($data);
        return $article->fresh();
    }

    public function delete(Article $article): void
    {
        $article->delete();
    }
}
