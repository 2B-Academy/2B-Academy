<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QualificationSkill extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    public function courses()
    {
        return $this->belongsToMany(
            Course::class,
            'course_qualification_skills',
            'qualification_skill_id',
            'course_id',
        );
    }
}
