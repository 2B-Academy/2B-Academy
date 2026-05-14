<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API v1 Routes
|--------------------------------------------------------------------------
| All routes are prefixed /api/v1 (the /api prefix is added by bootstrap/app.php).
| Domain-specific route files live in routes/apis/.
*/

Route::prefix('v1')->group(function () {

    // -----------------------------------------------------------------------
    // Auth (public — no Sanctum guard required)
    // -----------------------------------------------------------------------
    // e.g. POST /api/v1/auth/login
    // Populated in Phase 3 when API controllers are built.

    // -----------------------------------------------------------------------
    // Legacy webhooks (preserved from original api.php)
    // -----------------------------------------------------------------------
    Route::middleware('api-protect')->group(function () {
        Route::namespace('App\Http\Controllers\WebhookControllers')->group(function () {
            Route::post('/webhooks/user/create-or-update', 'UserController@createOrUpdate');
            Route::delete('/webhooks/user/delete/{system_id}', 'UserController@destroy');
        });
    });

    // -----------------------------------------------------------------------
    // Domain route files (populated progressively in Phase 3)
    // -----------------------------------------------------------------------
    // Each file follows the pattern: routes/apis/{domain}.php
    $apiRoutes = glob(base_path('routes/apis/*.php'));
    foreach ($apiRoutes as $route) {
        require $route;
    }

});
