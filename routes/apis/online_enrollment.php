<?php

use App\Http\Controllers\apis\OnlineEnrollmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Online Course Enrollment Routes — /api/v1/courses/{course}/online-users
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('courses/{course}/online-users',    [OnlineEnrollmentController::class, 'index']);
    Route::post('courses/{course}/online-users',   [OnlineEnrollmentController::class, 'store']);
    Route::put('courses/{course}/online-users',    [OnlineEnrollmentController::class, 'update']);
    Route::delete('courses/{course}/online-users', [OnlineEnrollmentController::class, 'destroy']);
});
