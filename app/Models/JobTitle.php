<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JobTitle extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'name_ar', 'name_en'];

    public function qualificationSkills(): BelongsToMany
    {
        return $this->belongsToMany(
            QualificationSkill::class,
            'job_title_qualification_skill',
            'job_title_id',
            'qualification_skill_id',
        )->withTimestamps();
    }

    /**
     * Employees holding this job title.
     *
     * The 2026 admin Users redesign dropped the denormalized
     * `users.job_title` string column, and the
     * `2026_05_25_140000_add_job_title_id_to_users_table` migration
     * re-established the link as a proper FK on `users.job_title_id`.
     * HR sync (`GetAllEmployeesFromHRSystemCommand`) is responsible
     * for keeping that FK aligned with the HR-side `jobName` field;
     * until it runs against a freshly-migrated database, the
     * `employees_count` on each job-title card legitimately reads `0`.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'job_title_id');
    }

    /**
     * Return the display name resolved for the current application locale.
     *
     * `name` is the HR-synced value (typically Arabic).
     * Priority (AR): name_ar → name
     * Priority (EN): name_en → name
     */
    public function getLocalizedName(): string
    {
        $locale = app()->getLocale();

        if ($locale === 'ar') {
            return (string) ($this->name_ar ?: $this->name);
        }

        return (string) ($this->name_en ?: $this->name);
    }
}
