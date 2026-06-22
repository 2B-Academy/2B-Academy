<?php

namespace App\Repositories\Eloquents;

use App\Models\Evaluation;
use App\Repositories\Contracts\EvaluationRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EvaluationRepository extends BaseRepository implements EvaluationRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new Evaluation());
    }

    public function paginateWithFilters(int $perPage = 20, ?string $search = null, ?int $categoryId = null): LengthAwarePaginator
    {
        return Evaluation::with('category')
            ->when($search, fn ($q) => $q->where('title->ar', 'like', "%$search%")
                ->orWhere('title->en', 'like', "%$search%")
            )
            ->when($categoryId, fn ($q) => $q->where('evaluation_category_id', $categoryId))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function allForCategory(int $categoryId): Collection
    {
        return Evaluation::where('evaluation_category_id', $categoryId)->orderBy('id')->get();
    }

    public function findWithCategory(int $id): Evaluation
    {
        return Evaluation::with('category')->findOrFail($id);
    }
}
