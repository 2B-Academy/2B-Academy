<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function qualificationSkill(): BelongsTo
    {
        return $this->belongsTo(QualificationSkill::class);
    }
}
