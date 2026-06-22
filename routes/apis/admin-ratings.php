<?php

use App\Http\Controllers\apis\Admin\AdminRatingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Ratings Routes — /api/v1/admin/ratings
|--------------------------------------------------------------------------
|
| New endpoints serving the platform-wide Ratings overview defined by the
| 2026 Figma redesign. The legacy ratings endpoints in
| routes/apis/ratings.php (course-scoped CRUD + learner submit) remain
| untouched and continue to serve the existing API surface.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {

    // Lookup endpoints
    Route::get('ratings/summary',         [AdminRatingController::class, 'summary']);
    Route::get('ratings/filter-options',  [AdminRatingController::class, 'filterOptions']);

    // Paginated list
    Route::get('ratings',                 [AdminRatingController::class, 'index']);
});
