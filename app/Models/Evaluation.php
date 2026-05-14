<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Evaluation extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title'];

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(EvaluationCategory::class,'evaluation_category_id');
    }
}
