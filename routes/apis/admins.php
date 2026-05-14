<?php

use App\Http\Controllers\apis\AdminController;
use App\Http\Controllers\apis\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin & Dashboard Routes — /api/v1/admins, /api/v1/dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {

    // Dashboard statistics
    Route::get('dashboard', [DashboardController::class, 'index']);

    // Admin CRUD
    Route::get('admins',           [AdminController::class, 'index']);
    Route::get('admins/{admin}',   [AdminController::class, 'show']);
    Route::post('admins',          [AdminController::class, 'store']);
    Route::put('admins/{admin}',   [AdminController::class, 'update']);
    Route::delete('admins/{admin}', [AdminController::class, 'destroy']);
});
