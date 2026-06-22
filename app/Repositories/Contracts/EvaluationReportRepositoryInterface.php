<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface EvaluationReportRepositoryInterface
{
    public function getEvaluationData(array $filters): array;
    public function getCategoryEvaluationData(array $filters): array;
}
