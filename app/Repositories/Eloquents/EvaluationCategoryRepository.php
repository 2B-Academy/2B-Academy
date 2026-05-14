<?php

namespace App\Repositories\Eloquents;

use App\Models\EvaluationCategory;
use App\Repositories\Contracts\EvaluationCategoryRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class EvaluationCategoryRepository extends BaseRepository implements EvaluationCategoryRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(new EvaluationCategory());
    }

    public function paginateWithSearch(int $perPage = 20, ?string $search = null): LengthAwarePaginator
    {
        return EvaluationCategory::when($search, fn ($q) => $q->where('name->ar', 'like', "%$search%")
                ->orWhere('name->en', 'like', "%$search%")
            )
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function all(): Collection
    {
        return EvaluationCategory::orderBy('id')->get();
    }
}
