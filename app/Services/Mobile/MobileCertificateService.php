<?php

declare(strict_types=1);

namespace App\Services\Mobile;

use App\Models\User;
use App\Models\UserCertificate;
use App\Repositories\Contracts\Mobile\MobileCertificateRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Thin orchestrator on top of MobileCertificateRepository. Lives so
 * the controller doesn't reach across layers, and so we have a
 * single place to add behaviour later (signed download URLs,
 * tracking, etc.) without touching the repository.
 */
final class MobileCertificateService
{
    public function __construct(
        private readonly MobileCertificateRepositoryInterface $repository,
        private readonly MobileSettings $settings,
    ) {}

    public function paginate(User $user, string $locale): LengthAwarePaginator
    {
        return $this->repository->paginate(
            $user,
            $locale,
            $this->settings->myLearningCertificatesPerPage(),
        );
    }

    public function preview(User $user, string $locale): Collection
    {
        return $this->repository->preview(
            $user,
            $locale,
            $this->settings->myLearningCertificatesPreviewCount(),
        );
    }

    public function findById(User $user, int $id, string $locale): ?UserCertificate
    {
        return $this->repository->findById($user, $id, $locale);
    }
}
