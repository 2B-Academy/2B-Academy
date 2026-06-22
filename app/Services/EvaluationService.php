<?php

namespace App\Services;

use App\Repositories\Contracts\EvaluationReportRepositoryInterface;

class EvaluationService
{
    public function __construct(
        private readonly EvaluationReportRepositoryInterface $repo
    ) {}

    public function getEvaluationData(array $filters = []): array
    {
        return $this->repo->getEvaluationData($filters);
    }

    public function getCategoryEvaluationData(array $filters = []): array
    {
        return $this->repo->getCategoryEvaluationData($filters);
    }
}
