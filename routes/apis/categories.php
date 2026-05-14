<?php

use App\Http\Controllers\apis\CategoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Category Routes — /api/v1/categories/*
|--------------------------------------------------------------------------
*/

// Public: active list for frontend dropdowns
Route::get('categories/active', [CategoryController::class, 'activeList']);

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('categories',               [CategoryController::class, 'index']);
    Route::get('categories/{category}',    [CategoryController::class, 'show']);
    Route::post('categories',              [CategoryController::class, 'store']);
    Route::put('categories/{category}',    [CategoryController::class, 'update']);
    Route::delete('categories/{category}', [CategoryController::class, 'destroy']);
});
