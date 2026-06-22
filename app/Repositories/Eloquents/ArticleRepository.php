<?php

namespace App\Repositories\Eloquents;

use App\Models\Article;
use App\Repositories\Contracts\ArticleRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ArticleRepository extends BaseRepository implements ArticleRepositoryInterface
{
    public function __construct(Article $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(int $perPage, ?string $type, ?string $search): LengthAwarePaginator
    {
        return $this->model->newQuery()
            ->when($type, fn ($q) => $q->where('type', $type))
            ->when($search, fn ($q) => $q->where('title->ar', 'like', "%$search%")
                ->orWhere('title->en', 'like', "%$search%"))
            ->orderByDesc('id')
            ->paginate($perPage);
    }
}
