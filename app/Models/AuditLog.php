<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_type',
        'user_id',
        'user_name',
        'action',
        'model_type',
        'model_id',
        'description',
        'ip_address',
    ];

    public $timestamps = true;
    const UPDATED_AT = null; // audit logs are never updated
}
