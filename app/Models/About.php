<?php

namespace App\Models;

use App\Http\Traits\HasFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class About extends Model
{
    use HasFactory, HasFile, HasTranslations;

    public array $translatable = ['about', 'mission', 'vision', 'goals'];

    protected $guarded = ['id'];

}
