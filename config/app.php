<?php

use Illuminate\Support\Facades\Facade;

return [

    /*
    |--------------------------------------------------------------------------
    | Custom Application Config
    |--------------------------------------------------------------------------
    */
    'paginate'       => 50,
    'API_SECRET_KEY' => env('API_SECRET_KEY'),

    /*
    |--------------------------------------------------------------------------
    | Application Name / Environment / Debug
    |--------------------------------------------------------------------------
    */
    'name'  => env('APP_NAME', 'Laravel'),
    'env'   => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),

    /*
    |--------------------------------------------------------------------------
    | Application URL
    |--------------------------------------------------------------------------
    */
    'url'       => env('APP_URL', 'http://localhost'),
    'asset_url' => env('ASSET_URL'),

    /*
    |--------------------------------------------------------------------------
    | Application Timezone / Locale
    |--------------------------------------------------------------------------
    */
    'timezone'        => env('APP_TIMEZONE', 'Africa/Cairo'),
    'locale'          => env('APP_LOCALE', 'ar'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'ar'),
    'faker_locale'    => env('APP_FAKER_LOCALE', 'en_US'),

    /*
    |--------------------------------------------------------------------------
    | Encryption Key
    |--------------------------------------------------------------------------
    */
    'key'    => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',

    /*
    |--------------------------------------------------------------------------
    | Maintenance Mode Driver
    |--------------------------------------------------------------------------
    */
    'maintenance' => [
        'driver' => 'file',
    ],

    /*
    |--------------------------------------------------------------------------
    | Class Aliases
    |--------------------------------------------------------------------------
    */
    'aliases' => Facade::defaultAliases()->merge([
        'Image' => Intervention\Image\Laravel\Facades\Image::class,
    ])->toArray(),

];
