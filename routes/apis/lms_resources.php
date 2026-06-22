<?php

use App\Http\Controllers\apis\LmsResourceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LMS Resource Routes — /api/v1/lms-resources/*
|--------------------------------------------------------------------------
*/

// Any authenticated user can browse resources
Route::middleware('auth.user')->group(function () {
    Route::get('lms-resources',              [LmsResourceController::class, 'index']);
    Route::get('lms-resources/{lms_resource}', [LmsResourceController::class, 'show']);
});

// Admin only — create / update / delete
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::post('lms-resources',                      [LmsResourceController::class, 'store']);
    Route::put('lms-resources/{lms_resource}',        [LmsResourceController::class, 'update']);
    Route::delete('lms-resources/{lms_resource}',     [LmsResourceController::class, 'destroy']);
});
