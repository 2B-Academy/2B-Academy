<?php

namespace App\Repositories\Contracts;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface TestimonialRepositoryInterface extends BaseRepositoryInterface
{
    public function paginateLatest(int $perPage): LengthAwarePaginator;
    public function allActive(): Collection;
}
