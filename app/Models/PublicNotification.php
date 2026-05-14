<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PublicNotification extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = ['title', 'body'];

    protected $guarded = ['id'];

    public function users()
    {
        return $this->hasMany(PublicNotificationUser::class, 'public_notification_id');
    }

}
