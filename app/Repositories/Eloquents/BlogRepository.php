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
        $jobTitleIds      = $filters['job_title_ids'] ?? null;
        $onlyPublished    = $filters['only_published'] ?? false;

        return $this->model->newQuery()
            ->with(['author', 'creator', 'qualificationSkills'])
            ->when($onlyPublished, fn ($q) => $q->published())
            ->when($search, fn ($q) => $q->where(function ($q) use ($search) {
                $q->where('title->ar', 'like', "%{$search}%")
                    ->orWhere('title->en', 'like', "%{$search}%")
                    ->orWhere('subtitle->ar', 'like', "%{$search}%")
                    ->orWhere('subtitle->en', 'like', "%{$search}%");
            }))
            ->when($level, fn ($q) => $q->whereIn('level', is_array($level) ? $level : explode(',', $level)))
            ->when($qualificationId, fn ($q) => $q->whereHas('qualificationSkills', fn ($s) =>
                $s->where('qualification_skills.id', $qualificationId)))
            ->when(is_array($qualificationIds) && count($qualificationIds), fn ($q) =>
                $q->whereHas('qualificationSkills', fn ($s) =>
                    $s->whereIn('qualification_skills.id', $qualificationIds)))
            // Filter by job title → blogs tagged with any qualification mapped
            // to any of the selected job titles (job_title_qualification_skill).
            ->when(is_array($jobTitleIds) && count($jobTitleIds), fn ($q) =>
                $q->whereHas('qualificationSkills', fn ($s) =>
                    $s->whereIn('qualification_skills.id', function ($sub) use ($jobTitleIds) {
                        $sub->select('qualification_skill_id')
                            ->from('job_title_qualification_skill')
                            ->whereIn('job_title_id', $jobTitleIds);
                    })))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findBySlug(string $slug, bool $onlyPublished = true): ?Blog
    {
        return $this->model->newQuery()
            ->with(['author', 'creator', 'qualificationSkills', 'sections'])
            ->withCount('likes')
            ->when($onlyPublished, fn ($q) => $q->published())
            ->where('slug', $slug)
            ->first();
    }

    public function related(Blog $blog, int $limit = 3): Collection
    {
        // Prefer blogs sharing at least one of this blog's qualification skills.
        $qualIds = $blog->qualificationSkills()->pluck('qualification_skills.id')->all();

        return $this->model->newQuery()
            ->with(['author', 'qualificationSkills'])
            ->published()
            ->whereKeyNot($blog->getKey())
            ->when($qualIds, fn ($q) => $q->withCount(['qualificationSkills as shared_qualifications_count' => fn ($s) =>
                    $s->whereIn('qualification_skills.id', $qualIds)])
                ->orderByDesc('shared_qualifications_count'))
            ->orderByDesc('published_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }
}
