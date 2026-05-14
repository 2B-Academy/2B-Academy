<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicNotificationUser extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function notification()
    {
        return $this->belongsTo(PublicNotification::class, 'public_notification_id');
    }

}
