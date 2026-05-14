<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Career extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getTitleAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->title_ar ?? $this->title_en;
        return $this->title_en;
    }

    public function getSlugAttribute()
    {
        if(currentLanguage() == 'ar')
            return arabicSlug($this->title_ar) ?? Str::slug($this->title_en);
        return Str::slug($this->title_en);
    }

    public function getDescriptionAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->description_ar ?? $this->description_en;
        return $this->description_en;
    }

    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }

    public function applications()
    {
        return $this->hasMany(CareerApplication::class, 'career_id');
    }

}
