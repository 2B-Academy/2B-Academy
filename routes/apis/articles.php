<?php

use App\Http\Controllers\apis\ArticleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Article Routes — /api/v1/articles
|--------------------------------------------------------------------------
*/

// Public: read-only
Route::get('articles',          [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::post('articles',             [ArticleController::class, 'store']);
    Route::put('articles/{article}',    [ArticleController::class, 'update']);
    Route::delete('articles/{article}', [ArticleController::class, 'destroy']);
});
