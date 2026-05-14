<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fact extends Model
{
    use HasFactory,HasFile;
    protected $guarded = ['id'];

    public function getTitleAttribute()
    {
        if(currentLanguage() == 'ar')
            return $this->title_ar ?? $this->title_en;
        return $this->title_en;
    }
    public function scopeActive($q)
    {
        return $q->whereActive(true);
    }
}
