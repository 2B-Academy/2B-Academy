<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * Eloquent model behind the additive 2026 Certificates redesign.
 *
 * Only ONE row is `is_active = true` at a time — uploading a new
 * template deactivates the previous active row (see
 * {@see \App\Services\Admin\AdminCertificateService::uploadTemplate()}).
 */
class CertificateTemplate extends Model
{
    use HasFactory;

    protected $table = 'certificate_templates';

    protected $fillable = [
        'name',
        'name_ar',
        'description',
        'description_ar',
        'auto_fields',
        'file_path',
        'original_filename',
        'mime_type',
        'file_size',
        'uploaded_by',
        'is_active',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'file_size'   => 'integer',
        'auto_fields' => 'array',
    ];

    /* ------------------------------------------------------------------ *
     |  Scopes                                                            |
     * ------------------------------------------------------------------ */

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    /* ------------------------------------------------------------------ *
     |  Relations                                                         |
     * ------------------------------------------------------------------ */

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'uploaded_by');
    }

    /* ------------------------------------------------------------------ *
     |  Accessors                                                         |
     * ------------------------------------------------------------------ */

    /**
     * Absolute URL to the uploaded template image (PNG/JPG/PDF), or null
     * when only the placeholder row exists with no file uploaded yet.
     */
    public function getFileUrlAttribute(): ?string
    {
        if (empty($this->file_path)) {
            return null;
        }

        if (config('filesystems.default') === 's3') {
            return Storage::disk('s3')->url($this->file_path);
        }

        return url(Storage::disk('public')->url($this->file_path));
    }
}
