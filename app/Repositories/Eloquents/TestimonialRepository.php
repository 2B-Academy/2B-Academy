<?php

namespace App\Repositories\Eloquents;

use App\Models\Testimonial;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class TestimonialRepository extends BaseRepository implements TestimonialRepositoryInterface
{
    public function __construct(Testimonial $model)
    {
        parent::__construct($model);
    }

    public function paginateLatest(int $perPage): LengthAwarePaginator
    {
        return $this->model->newQuery()->orderByDesc('id')->paginate($perPage);
    }

    public function allActive(): Collection
    {
        return $this->model->newQuery()->active()->get();
    }
}
