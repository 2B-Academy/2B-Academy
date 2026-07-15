<?php

use App\Http\Controllers\apis\BlogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Blog Routes — /api/v1
|--------------------------------------------------------------------------
| Public (website, SEO surface): published blogs only, no auth.
| Admin (dashboard): full CRUD incl. drafts, guarded by auth.user + role:Admin.
*/

// ── Public ───────────────────────────────────────────────────────────────
Route::get('blogs',                 [BlogController::class, 'index']);
Route::get('blogs/{slug}/related',  [BlogController::class, 'related']);
Route::get('blogs/{slug}',          [BlogController::class, 'show']);

// ── Learner (website "Tailored for Me") ────────────────────────────────────
Route::middleware('auth.user')->get('learner/blogs', [BlogController::class, 'tailoredIndex']);

// ── Admin (dashboard) ──────────────────────────────────────────────────────
Route::prefix('admin')->middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('blogs',            [BlogController::class, 'adminIndex']);
    Route::get('blogs/{blog}',     [BlogController::class, 'adminShow']);
    Route::post('blogs',           [BlogController::class, 'store']);
    Route::put('blogs/{blog}',     [BlogController::class, 'update']);
    Route::delete('blogs/{blog}',  [BlogController::class, 'destroy']);
});
