<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserFormAnswers extends Model
{
    use HasFactory;
    protected $guarded = ['id'];


    public function userForm()
    {
        return $this->belongsTo(UserForm::class, 'user_form_id');
    }

    public function mainQuestion()
    {
        return $this->belongsTo(FormQuestion::class, 'question_id');
    }

}
