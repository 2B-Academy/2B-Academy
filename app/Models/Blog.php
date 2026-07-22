<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Blog extends Model
{
    use HasFactory, HasFile, HelperTrait, HasTranslations;

    public array $translatable = ['title', 'subtitle'];

    protected $guarded = ['id'];

    protected $casts = [
        'is_anonymous' => 'boolean',
        'active'       => 'boolean',
        'reading_time' => 'integer',
        'published_at' => 'date',
    ];

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeActive($q)
    {
        return $q->where('active', true);
    }

    /**
     * Publicly visible blogs: active and either not scheduled or already due.
     */
    public function scopePublished($q)
    {
        return $q->where('active', true)
            ->where(function ($q) {
                $q->whereNull('published_at')
                    ->orWhereDate('published_at', '<=', now());
            });
    }

    /*
    |--------------------------------------------------------------------------
    | Mutators
    |--------------------------------------------------------------------------
    */

    public function setSlugAttribute($value): void
    {
        $this->attributes['slug'] = arabicSlug($value);
    }

    /*
    |--------------------------------------------------------------------------
    | Relations
    |--------------------------------------------------------------------------
    */

    public function sections(): HasMany
    {
        return $this->hasMany(BlogSection::class)->orderBy('sort_order');
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_user_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }

    /**
     * Qualification skills this blog surfaces under. A blog can now be tagged
     * with several qualifications (was a single `qualification_skill_id` FK).
     */
    public function qualificationSkills(): BelongsToMany
    {
        return $this->belongsToMany(
            QualificationSkill::class,
            'blog_qualification_skill',
            'blog_id',
            'qualification_skill_id',
        );
    }
}
