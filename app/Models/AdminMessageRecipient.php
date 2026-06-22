<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdminMessageRecipient extends Model
{
    use HasFactory;

    protected $fillable = ['admin_message_id', 'user_id', 'read_at'];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function adminMessage(): BelongsTo
    {
        return $this->belongsTo(AdminMessage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
