<?php

use App\Http\Controllers\apis\CourseAssignmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Assignment Routes — /api/v1/courses/{course}/assignments
|--------------------------------------------------------------------------
*/

// Admin: full management + submission review
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('assignments',                                                                           [CourseAssignmentController::class, 'indexAll']);
    Route::get('assignments/submissions',                                                               [CourseAssignmentController::class, 'allSubmissions']);
    Route::get('courses/{course}/assignments',                                                          [CourseAssignmentController::class, 'index']);
    Route::post('courses/{course}/assignments',                                                        [CourseAssignmentController::class, 'store']);
    Route::put('courses/{course}/assignments/{assignment}',                                            [CourseAssignmentController::class, 'update']);
    Route::delete('courses/{course}/assignments/{assignment}',                                         [CourseAssignmentController::class, 'destroy']);
    Route::get('courses/{course}/assignments/{assignment}/submissions',                                [CourseAssignmentController::class, 'submissions']);
    Route::put('courses/{course}/assignments/{assignment}/submissions/{submission}/review',            [CourseAssignmentController::class, 'review']);
});

// User: view assignments for enrolled course, submit file, view own submission
Route::middleware(['auth.user', 'role:User'])->group(function () {
    Route::get('courses/{course}/assignments',                                                         [CourseAssignmentController::class, 'index']);
    Route::post('courses/{course}/assignments/{assignment}/submit',                                    [CourseAssignmentController::class, 'submit']);
    Route::get('courses/{course}/assignments/{assignment}/my-submission',                              [CourseAssignmentController::class, 'mySubmission']);
});
