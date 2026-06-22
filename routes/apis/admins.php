<?php

use App\Http\Controllers\apis\AdminController;
use App\Http\Controllers\apis\DashboardController;
use App\Http\Controllers\apis\DashboardPasscodeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin & Dashboard Routes — /api/v1/admins, /api/v1/dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {

    // Dashboard statistics
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Dashboard passcode widget — read current live-session state and
    // generate a passcode for it in one tap (drives mobile S-06).
    Route::get('dashboard/passcode',          [DashboardPasscodeController::class, 'current']);
    Route::get('dashboard/passcode/courses',  [DashboardPasscodeController::class, 'courses']);
    Route::post('dashboard/passcode',         [DashboardPasscodeController::class, 'generate']);
    Route::post('dashboard/passcode/regenerate', [DashboardPasscodeController::class, 'regenerate']);
    Route::post('dashboard/passcode/end',        [DashboardPasscodeController::class, 'end']);

    // Admin CRUD
    Route::get('admins',           [AdminController::class, 'index']);
    Route::get('admins/{admin}',   [AdminController::class, 'show']);
    Route::post('admins',          [AdminController::class, 'store']);
    Route::put('admins/{admin}',   [AdminController::class, 'update']);
    Route::delete('admins/{admin}', [AdminController::class, 'destroy']);
});
