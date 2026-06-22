<?php

namespace App\Services;

use App\Models\JobTitle;
use App\Repositories\Contracts\JobTitleRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class JobTitleService
{
    public function __construct(
        private readonly JobTitleRepositoryInterface $repository,
    ) {}

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->repository->list($perPage, $search);
    }

    public function allForSelect(): Collection
    {
        return $this->repository->allForSelect();
    }

    public function syncQualifications(JobTitle $jobTitle, array $qualIds): JobTitle
    {
        $this->repository->syncQualifications($jobTitle, $qualIds);

        return $jobTitle->load('qualificationSkills');
    }
}
