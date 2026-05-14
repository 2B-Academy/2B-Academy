<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seo extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getMetaTitleAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->meta_title_ar ?? $this->meta_title_en;
        return $this->meta_title_en;
    }
    public function getMetaDescriptionAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->meta_description_ar ?? $this->meta_description_en;
        return $this->meta_description_en;
    }

    public function getSiteNameAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->site_name_ar ?? $this->site_name_en;
        return $this->site_name_en;
    }

    public function getAuthorAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->author_ar ?? $this->author_en;
        return $this->author_en;
    }
    public function getMetaKeywordsAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->meta_keywords_ar ?? $this->meta_keywords_en;
        return $this->meta_keywords_en;
    }


}
