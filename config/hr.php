<?php

return [
    /*
    |--------------------------------------------------------------------------
    | HR System Integration
    |--------------------------------------------------------------------------
    |
    | Settings for the external HR API used for employee authentication and
    | data sync. Read these through config() (never env() at runtime) so the
    | values keep working after `php artisan config:cache` / `optimize`.
    |
    */

    'base_url'       => env('HR_BASE_URL'),
    'verify_ssl'     => env('HR_VERIFY_SSL', true),
    'admin_email'    => env('HR_ADMIN_EMAIL'),
    'admin_password' => env('HR_ADMIN_PASSWORD'),
];
