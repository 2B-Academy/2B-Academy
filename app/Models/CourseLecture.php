<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseLecture extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['title'];

    protected $guarded = ['id'];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }
}
