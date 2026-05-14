<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseExamQuestion extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['question'];

    protected $guarded = ['id'];

    public function answers()
    {
        return $this->hasMany(CourseExamQuestionAnswer::class, 'question_id');
    }
}
