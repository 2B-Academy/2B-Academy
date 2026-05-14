<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Testimonial extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['name', 'description'];

    protected $guarded = ['id'];

    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }

}
