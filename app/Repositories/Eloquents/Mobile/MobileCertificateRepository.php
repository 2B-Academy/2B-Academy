<?php

declare(strict_types=1);

namespace App\Repositories\Eloquents\Mobile;

use App\Models\User;
use App\Models\UserCertificate;
use App\Repositories\Contracts\Mobile\MobileCertificateRepositoryInterface;
use App\Repositories\Contracts\UserCertificateRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Read-only mobile view over the first-class `user_certificates` table.
 *
 * Delegates straight to the shared UserCertificateRepository so the
 * mobile surface and the admin/web surface read certificates through the
 * exact same persistence layer — no second source of truth, no
 * derivation, no compound ids.
 */
final class MobileCertificateRepository implements MobileCertificateRepositoryInterface
{
    public function __construct(
        private readonly UserCertificateRepositoryInterface $certificates,
    ) {}

    public function paginate(User $user, string $locale, int $perPage): LengthAwarePaginator
    {
        return $this->certificates->paginateForUser((int) $user->id, $perPage);
    }

    public function preview(User $user, string $locale, int $limit): Collection
    {
        return $this->certificates->previewForUser((int) $user->id, $limit);
    }

    public function findById(User $user, int $id, string $locale): ?UserCertificate
    {
        $certificate = $this->certificates->findForUser((int) $user->id, $id);

        return $certificate && $certificate->isActive() ? $certificate : null;
    }
}
