<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CourseLecture extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['title', 'instructions'];

    protected $guarded = ['id'];

    protected $casts = [
        'duration_minutes'   => 'integer',
        'require_completion' => 'boolean',
    ];

    public function section()
    {
        return $this->belongsTo(CourseSection::class, 'section_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function session()
    {
        return $this->belongsTo(CourseSession::class, 'session_id');
    }
}
