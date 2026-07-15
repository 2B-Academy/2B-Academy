<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Blog;
use App\Repositories\Contracts\BlogRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class BlogService
{
    use HasFile;

    public function __construct(
        private readonly BlogRepositoryInterface $repo
    ) {}

    /*
    |--------------------------------------------------------------------------
    | Reads
    |--------------------------------------------------------------------------
    */

    public function paginate(int $perPage, array $filters = []): LengthAwarePaginator
    {
        return $this->repo->paginateWithFilters($perPage, $filters);
    }

    public function findBySlug(string $slug, bool $onlyPublished = true): ?Blog
    {
        return $this->repo->findBySlug($slug, $onlyPublished);
    }

    public function findForEdit(Blog $blog): Blog
    {
        return $blog->load(['author', 'creator', 'qualificationSkill', 'sections']);
    }

    public function related(Blog $blog, int $limit = 3): Collection
    {
        return $this->repo->related($blog, $limit);
    }

    /*
    |--------------------------------------------------------------------------
    | Writes
    |--------------------------------------------------------------------------
    */

    public function create(Request $request): Blog
    {
        $data = $request->validated();

        return DB::transaction(function () use ($request, $data) {
            $attributes = $this->baseAttributes($data);
            $attributes['slug']               = $this->uniqueSlug($this->titleForSlug($data));
            $attributes['created_by_admin_id'] = $request->user()?->id;
            $attributes['image']              = $request->hasFile('image')
                ? $this->uploadRequestFile('Blog', request(), 'image')
                : null;

            /** @var Blog $blog */
            $blog = $this->repo->create($attributes);

            $this->syncSections($blog, $request, $data['sections'] ?? []);

            return $this->findForEdit($blog);
        });
    }

    public function update(Blog $blog, Request $request): Blog
    {
        $data = $request->validated();

        return DB::transaction(function () use ($blog, $request, $data) {
            $attributes = $this->baseAttributes($data);
            if ($request->hasFile('image')) {
                $attributes['image'] = $this->uploadRequestFile('Blog', request(), 'image');
            }
            // Keep the existing slug stable across edits.

            $this->repo->update($blog, $attributes);

            $this->syncSections($blog, $request, $data['sections'] ?? []);

            return $this->findForEdit($blog->refresh());
        });
    }

    public function delete(Blog $blog): void
    {
        $this->repo->delete($blog); // sections cascade via FK
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    /**
     * Column values shared by create + update (excludes slug / image / creator).
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    private function baseAttributes(array $data): array
    {
        return [
            'title'                  => $data['title'],
            'subtitle'               => $data['subtitle'] ?? null,
            'level'                  => $data['level'],
            'is_anonymous'           => (bool) ($data['is_anonymous'] ?? false),
            'author_user_id'         => ($data['is_anonymous'] ?? false) ? null : ($data['author_user_id'] ?? null),
            'reading_time'           => $data['reading_time'],
            'qualification_skill_id' => $data['qualification_skill_id'] ?? null,
            'active'                 => (bool) ($data['active'] ?? true),
            'published_at'           => $data['published_at'] ?? now()->toDateString(),
        ];
    }

    /**
     * Create/update/prune a blog's sections to match the submitted payload.
     *
     * @param  array<int, array<string, mixed>>  $sections
     */
    private function syncSections(Blog $blog, Request $request, array $sections): void
    {
        $keptIds = [];

        foreach (array_values($sections) as $index => $section) {
            $attributes = [
                'title'      => $section['title'],
                'body'       => $section['body'],
                'quote'      => ($section['quote'] ?? null) ?: null,
                'sort_order' => $section['sort_order'] ?? $index,
                'image'      => $this->resolveSectionImage($request, $index, $section),
            ];

            $existingId = isset($section['id']) ? (int) $section['id'] : null;
            $model = $existingId ? $blog->sections()->whereKey($existingId)->first() : null;

            if ($model) {
                $model->update($attributes);
            } else {
                $model = $blog->sections()->create($attributes);
            }

            $keptIds[] = $model->id;
        }

        $blog->sections()->whereKeyNot($keptIds)->delete();
    }

    /**
     * A section image can arrive as a fresh upload (multipart file) or as the
     * previously-stored path string on edit. Anything else clears it.
     *
     * @param  array<string, mixed>  $section
     */
    private function resolveSectionImage(Request $request, int $index, array $section): ?string
    {
        $file = $request->file("sections.{$index}.image");
        if ($file instanceof UploadedFile) {
            return $this->uploadRequestFile('Blog', request(), "sections.{$index}.image");
        }

        $value = $section['image'] ?? null;

        return is_string($value) && $value !== '' ? $value : null;
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function titleForSlug(array $data): string
    {
        return (string) ($data['title']['en'] ?? $data['title']['ar'] ?? 'blog');
    }

    private function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = arabicSlug($title) ?: 'blog';
        $slug = $base;
        $suffix = 2;

        while (
            Blog::query()
                ->where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->whereKeyNot($ignoreId))
                ->exists()
        ) {
            $slug = "{$base}-{$suffix}";
            $suffix++;
        }

        return $slug;
    }
}
