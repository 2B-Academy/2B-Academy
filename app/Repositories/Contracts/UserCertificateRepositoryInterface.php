<?php

namespace App\Repositories\Contracts;

use App\Models\UserCertificate;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Persistence + read surface for the first-class certificate entity
 * (`user_certificates`). This is the ONLY place that touches the table —
 * issuance/revocation orchestration lives in CertificateService, which
 * delegates all storage here.
 */
interface UserCertificateRepositoryInterface
{
    public function create(array $attributes): UserCertificate;

    public function findById(int $id): ?UserCertificate;

    /** Scoped lookup — only returns the certificate if it belongs to the user. */
    public function findForUser(int $userId, int $id): ?UserCertificate;

    public function findActiveByUserAndCourse(int $userId, int $courseId): ?UserCertificate;

    public function findAnyByUserAndCourse(int $userId, int $courseId): ?UserCertificate;

    /** Newest-first, active only, scoped to a learner. */
    public function paginateForUser(int $userId, int $perPage): LengthAwarePaginator;

    /** @return Collection<int, UserCertificate> */
    public function previewForUser(int $userId, int $limit): Collection;

    /** Admin issued list with optional search (learner / number / course) + course filter. */
    public function paginateAdmin(int $perPage, ?string $search, ?int $courseId): LengthAwarePaginator;

    public function countActive(): int;

    /**
     * Greatest existing certificate_number for a given year, taking a
     * row lock so concurrent issuance inside a transaction stays unique.
     */
    public function latestNumberForYear(int $year): ?string;
}
