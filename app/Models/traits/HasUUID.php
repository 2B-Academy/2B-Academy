<?php

namespace App\Models\traits;

trait HasUUID
{
    public static function bootHasUUID()
    {
        static::creating(function ($model) {
            $model->uuid = (string) \Illuminate\Support\Str::uuid();
        });

    }


}
