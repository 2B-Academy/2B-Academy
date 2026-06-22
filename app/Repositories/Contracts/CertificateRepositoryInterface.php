<?php

namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface CertificateRepositoryInterface
{
    public function getExamCertificates(?int $courseId): Collection;
    public function getEvalCertificates(?int $courseId): Collection;
}
