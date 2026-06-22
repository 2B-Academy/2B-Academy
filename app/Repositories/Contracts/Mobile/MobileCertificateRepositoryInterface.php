<?php

declare(strict_types=1);

namespace App\Repositories\Contracts\Mobile;

use App\Models\User;
use App\Models\UserCertificate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Read surface for S-07 (Certificates).
 *
 * Certificates are now first-class rows in `user_certificates` — the
 * compound-id (`exam:123` / `evaluation:456`) derivation is gone. Every
 * lookup is by the certificate's own integer id, scoped to the learner.
 */
interface MobileCertificateRepositoryInterface
{
    /**
     * Paginated active certificates for the user (newest first).
     *
     * @return LengthAwarePaginator<UserCertificate>
     */
    public function paginate(User $user, string $locale, int $perPage): LengthAwarePaginator;

    /**
     * Top-N for the My Learning overview.
     *
     * @return Collection<int, UserCertificate>
     */
    public function preview(User $user, string $locale, int $limit): Collection;

    /**
     * Look up a single certificate by its integer id, scoped to the user.
     * Returns null when it doesn't exist or belongs to someone else.
     */
    public function findById(User $user, int $id, string $locale): ?UserCertificate;
}
