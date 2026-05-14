<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Http\Traits\HasFile;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

}
