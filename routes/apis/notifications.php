<?php

use App\Http\Controllers\apis\NotificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Notification Routes — /api/v1/notifications
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('notifications',                [NotificationController::class, 'index']);
    Route::get('notifications/{notification}', [NotificationController::class, 'show']);
    Route::post('notifications',               [NotificationController::class, 'store']);
    Route::put('notifications/{notification}', [NotificationController::class, 'update']);
    Route::delete('notifications/{notification}', [NotificationController::class, 'destroy']);
});
