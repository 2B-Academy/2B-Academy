<?php

namespace App\Repositories\Contracts;

use App\Models\JobTitle;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface JobTitleRepositoryInterface extends BaseRepositoryInterface
{
    public function list(int $perPage, ?string $search): LengthAwarePaginator;

    public function allForSelect(): Collection;

    public function syncQualifications(JobTitle $jobTitle, array $qualIds): void;
}
