<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdminMessage extends Model
{
    use HasFactory;

    protected $fillable = ['admin_id', 'subject', 'body'];

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(AdminMessageRecipient::class);
    }
}
