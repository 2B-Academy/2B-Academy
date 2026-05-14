<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use App\Http\Traits\HelperTrait;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Article extends Model
{
    use HasFactory, HasFile, HelperTrait, HasTranslations;

    public array $translatable = ['title', 'description'];

    protected $guarded = ['id'];

    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }

    public function setDatePublishAttribute($value)
    {
        $this->attributes['date_publish'] = date('Y-m-d',strtotime($value));
    }

    public function setSlugAttribute($value)
    {
        $this->attributes['slug'] = arabicSlug($value);
    }

}
