<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * First-class certificate business document.
 *
 * A certificate is issued ONCE per learner+course (one active row) by
 * App\Services\CertificateService and then lives independently of the
 * exam / evaluation that triggered it. The originating record is kept in
 * `source_type` / `source_id` for audit only — it is never exposed to
 * API consumers, which see only the integer `id` (and `uuid` /
 * `certificate_number`).
 */
class UserCertificate extends Model
{
    use HasFactory;

    public const STATUS_ACTIVE  = 'active';
    public const STATUS_REVOKED = 'revoked';

    public const SOURCE_EXAM       = 'exam';
    public const SOURCE_EVALUATION = 'evaluation';

    protected $guarded = ['id'];

    protected $casts = [
        'metadata'   => 'array',
        'issued_at'  => 'datetime',
        'revoked_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $certificate): void {
            if (empty($certificate->uuid)) {
                $certificate->uuid = (string) Str::uuid();
            }
            if (empty($certificate->status)) {
                $certificate->status = self::STATUS_ACTIVE;
            }
            if (empty($certificate->issued_at)) {
                $certificate->issued_at = now();
            }
        });
    }

    /* ── Relationships ─────────────────────────────────────────────── */

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function issuer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'issued_by');
    }

    public function revoker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    /* ── Scopes ────────────────────────────────────────────────────── */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeRevoked(Builder $query): Builder
    {
        return $query->where('status', self::STATUS_REVOKED);
    }

    /* ── Helpers ───────────────────────────────────────────────────── */

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isRevoked(): bool
    {
        return $this->status === self::STATUS_REVOKED;
    }

    /**
     * Localized course title printed on the certificate. Prefers the
     * persisted snapshot in `metadata` (so the historical document never
     * changes if the course is later renamed); falls back to the live
     * course translation.
     */
    public function localizedCourseTitle(?string $locale = null): string
    {
        $locale ??= app()->getLocale();

        $snapshot = $this->metadata['course_title'] ?? null;
        if (is_array($snapshot)) {
            $value = $snapshot[$locale] ?? $snapshot['en'] ?? $snapshot['ar'] ?? null;
            if (is_string($value) && $value !== '') {
                return $value;
            }
        }

        $course = $this->course;
        if ($course) {
            return (string) ($course->getTranslation('title_for_certificate', $locale)
                ?: $course->getTranslation('title', $locale));
        }

        return '';
    }
}
