<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Spatie\Translatable\HasTranslations;

class Instructor extends Model
{
    use HasFactory, HasFile, HasTranslations, Notifiable;

    public array $translatable = ['name', 'bio'];

    protected $guarded = ['id'];

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'courses_instructors', 'instructor_id', 'course_id');
    }
}