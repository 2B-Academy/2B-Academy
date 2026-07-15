<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class BlogSection extends Model
{
    use HasFactory, HasFile, HelperTrait, HasTranslations;

    public array $translatable = ['title', 'body', 'quote'];

    protected $guarded = ['id'];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    public function blog(): BelongsTo
    {
        return $this->belongsTo(Blog::class);
    }
}
