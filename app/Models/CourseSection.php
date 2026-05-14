<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseSection extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    public function lectures()
    {
        return $this->hasMany(CourseLecture::class, 'section_id');
    }

    public function sessions()
    {
        return $this->hasMany(CourseSession::class, 'section_id');
    }

    public function exams()
    {
        return $this->hasMany(CourseExam::class, 'section_id');
    }

}
