<?php

namespace App\Repositories\Eloquents;

use App\Models\UserCertificate;
use App\Repositories\Contracts\UserCertificateRepositoryInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/**
 * Eloquent implementation — reads/writes ONLY the `user_certificates`
 * table. No exam/evaluation derivation lives here anymore.
 */
class UserCertificateRepository implements UserCertificateRepositoryInterface
{
    public function create(array $attributes): UserCertificate
    {
        return UserCertificate::create($attributes);
    }

    public function findById(int $id): ?UserCertificate
    {
        return UserCertificate::query()->with('course')->find($id);
    }

    public function findForUser(int $userId, int $id): ?UserCertificate
    {
        return UserCertificate::query()
            ->with('course')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();
    }

    public function findActiveByUserAndCourse(int $userId, int $courseId): ?UserCertificate
    {
        return UserCertificate::query()
            ->active()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();
    }

    public function findAnyByUserAndCourse(int $userId, int $courseId): ?UserCertificate
    {
        return UserCertificate::query()
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->latest('issued_at')
            ->first();
    }

    public function paginateForUser(int $userId, int $perPage): LengthAwarePaginator
    {
        return UserCertificate::query()
            ->with('course')
            ->active()
            ->where('user_id', $userId)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function previewForUser(int $userId, int $limit): Collection
    {
        return UserCertificate::query()
            ->with('course')
            ->active()
            ->where('user_id', $userId)
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();
    }

    public function paginateAdmin(int $perPage, ?string $search, ?int $courseId): LengthAwarePaginator
    {
        $needle = trim((string) $search);

        return UserCertificate::query()
            ->with(['user:id,name,machine_code,department_name', 'course:id,title,title_for_certificate'])
            ->active()
            ->when($courseId, fn ($q) => $q->where('course_id', $courseId))
            ->when($needle !== '', function ($q) use ($needle) {
                $like = '%'.$needle.'%';
                $q->where(function ($inner) use ($like) {
                    $inner->where('certificate_number', 'like', $like)
                        ->orWhereHas('user', fn ($u) => $u
                            ->where('name', 'like', $like)
                            ->orWhere('machine_code', 'like', $like))
                        ->orWhereHas('course', fn ($c) => $c->where('title', 'like', $like));
                });
            })
            ->orderByDesc('issued_at')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function countActive(): int
    {
        return UserCertificate::query()->active()->count();
    }

    public function latestNumberForYear(int $year): ?string
    {
        $query = UserCertificate::query()
            ->where('certificate_number', 'like', sprintf('CERT-%d-%%', $year))
            ->orderByDesc('certificate_number');

        // Lock the matching rows when called inside an open transaction so
        // two concurrent issuers can't mint the same sequential number.
        if (\Illuminate\Support\Facades\DB::transactionLevel() > 0) {
            $query->lockForUpdate();
        }

        return $query->value('certificate_number');
    }
}
