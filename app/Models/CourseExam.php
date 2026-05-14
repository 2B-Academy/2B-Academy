<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseExam extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title'];

    protected $guarded = ['id'];

    public function questions()
    {
        return $this->hasMany(CourseExamQuestion::class, 'course_exam_id');
    }

}
