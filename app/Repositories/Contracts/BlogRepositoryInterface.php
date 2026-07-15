<?php

namespace App\Repositories\Contracts;

use App\Models\Blog;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface BlogRepositoryInterface extends BaseRepositoryInterface
{
    /**
     * @param array{
     *     search?: ?string,
     *     level?: ?string,
     *     qualification_skill_id?: int|string|null,
     *     qualification_ids?: array<int>|null,
     *     only_published?: bool
     * } $filters
     */
    public function paginateWithFilters(int $perPage, array $filters = []): LengthAwarePaginator;

    public function findBySlug(string $slug, bool $onlyPublished = true): ?Blog;

    /**
     * Related published blogs — same qualification first, most recent, excluding self.
     *
     * @return \Illuminate\Support\Collection<int, Blog>
     */
    public function related(Blog $blog, int $limit = 3): \Illuminate\Support\Collection;
}
