<?php

namespace App\Models;

use App\Models\traits\HasUUID;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Form extends Model
{
    use HasFactory, HasUUID, HasTranslations;

    public array $translatable = ['title'];

    protected $guarded = ['id'];

    public function questions()
    {
        return $this->hasMany(FormQuestion::class);
    }

    public function users()
    {
        return $this->hasMany(UserForm::class, 'form_id');
    }

}
