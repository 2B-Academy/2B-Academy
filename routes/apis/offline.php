<?php

use App\Http\Controllers\apis\CourseSessionController;
use App\Http\Controllers\apis\UserEnrollmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Offline Course Routes — /api/v1/courses/{course}/sessions & enrollments
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {

    // Session management
    Route::get('courses/{course}/sessions',                  [CourseSessionController::class, 'index']);
    Route::post('courses/{course}/sessions',                 [CourseSessionController::class, 'store']);
    Route::put('courses/{course}/sessions/{session}',        [CourseSessionController::class, 'update']);
    Route::delete('courses/{course}/sessions/{session}',     [CourseSessionController::class, 'destroy']);

    // Offline enrollment management
    Route::get('courses/{course}/enrollments',               [UserEnrollmentController::class, 'index']);
    Route::post('courses/{course}/enrollments',              [UserEnrollmentController::class, 'store']);
    Route::delete('courses/{course}/enrollments/{enrollment}', [UserEnrollmentController::class, 'destroy']);
});
