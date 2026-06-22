<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LmsResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'content',
        'url',
        'file_path',
        'file_name',
        'file_size',
        'qualification_skill_id',
        'created_by_admin_id',
    ];

    protected $casts = [
        'type' => 'string',
    ];

    public function qualificationSkill(): BelongsTo
    {
        return $this->belongsTo(QualificationSkill::class);
    }

    public function createdByAdmin(): BelongsTo
    {
        return $this->belongsTo(Admin::class, 'created_by_admin_id');
    }
}
