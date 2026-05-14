<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EvaluationCategory extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['name'];

    protected $guarded = ['id'];

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

}
