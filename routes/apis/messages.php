<?php

use App\Http\Controllers\apis\AdminMessageController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Message Routes — /api/v1/messages/*
|--------------------------------------------------------------------------
*/

// Admin-only routes
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('messages',         [AdminMessageController::class, 'index']);
    Route::post('messages',        [AdminMessageController::class, 'store']);
    Route::get('messages/{message}', [AdminMessageController::class, 'show']);
});

// Authenticated user — mark message as read
Route::middleware('auth.user')->group(function () {
    Route::patch('messages/{message}/read', [AdminMessageController::class, 'markRead']);
});
