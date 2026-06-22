<?php

use App\Http\Controllers\apis\CourseRatingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Course Rating Routes — /api/v1/courses/{course}/ratings
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('ratings',                              [CourseRatingController::class, 'allRatings']);
    Route::get('courses/{course}/ratings',              [CourseRatingController::class, 'index']);
    Route::delete('courses/{course}/ratings/{rating}',  [CourseRatingController::class, 'destroy']);
});

Route::middleware(['auth.user', 'role:User'])->group(function () {
    Route::post('courses/{course}/ratings', [CourseRatingController::class, 'store']);
});
