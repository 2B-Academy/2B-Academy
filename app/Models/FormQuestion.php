<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class FormQuestion extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['question'];

    protected $guarded = ['id'];


    public function answers()
    {
        return $this->hasMany(FormQuestionAnswer::class);
    }

    public function userFormAnswers()
    {
        return $this->hasMany(UserFormAnswers::class, 'question_id');
    }
}
