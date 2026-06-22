<?php

use App\Http\Controllers\apis\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Routes — /api/v1/users/*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('users/search',       [UserController::class, 'search']);
    Route::get('users',              [UserController::class, 'index']);
    Route::get('users/{user}',       [UserController::class, 'show']);
    Route::post('users',             [UserController::class, 'store']);
    Route::put('users/{user}',       [UserController::class, 'update']);
    Route::delete('users/{user}',    [UserController::class, 'destroy']);
});
