<?php

namespace App\Repositories\Eloquents;

use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class BlogRepository extends BaseRepository implements BlogRepositoryInterface
{
    public function __construct(Blog $model)
    {
        parent::__construct($model);
    }

    public function paginateWithFilters(int $perPage, array $filters = []): LengthAwarePaginator
    {
        $search           = $filters['search'] ?? null;
        $level            = $filters['level'] ?? null;
        $qualificationId  = $filters['qualification_skill_id'] ?? null;
        $qualificationIds = $filters['qualification_ids'] ?? null;
        $onlyPublished    = $filters['only_published'] ?? false;

        return $this->model->newQuery()
            ->with(['author', 'creator', 'qualificationSkill'])
            ->when($onlyPublished, fn ($q) => $q->published())
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('title->ar', 'like', "%{$search}%")
                    ->orWhere('title->en', 'like', "%{$search}%")
                    ->orWhere('subtitle->ar', 'like', "%{$search}%")
                    ->orWhere('subtitle->en', 'like', "%{$search}%");
            }))
            ->when($level, fn ($q) => $q->where('level', $level))
            ->when($qualificationId, fn ($q) => $q->where('qualification_skill_id', $qualificationId))
            ->when(is_array($qualificationIds) && count($qualificationIds), fn ($q) =>
                $q->whereIn('qualification_skill_id', $qualificationIds))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug, bool $onlyPublished = true): ?Blog
    {
        return $this->model->newQuery()
            ->with(['author', 'creator', 'qualificationSkill', 'sections'])
            ->when($onlyPublished, fn ($q) => $q->published())
            ->where('slug', $slug)
            ->first();
    }

    public function related(Blog $blog, int $limit = 3): Collection
    {
        return $this->model->newQuery()
            ->with(['author', 'qualificationSkill'])
            ->published()
            ->whereKeyNot($blog->getKey())
            ->when($blog->qualification_skill_id, fn ($q) =>
                $q->orderByRaw('qualification_skill_id = ? desc', [$blog->qualification_skill_id]))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
