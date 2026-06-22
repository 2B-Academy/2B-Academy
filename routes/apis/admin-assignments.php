<?php

use App\Http\Controllers\apis\Admin\AdminAssignmentController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Rich-Assignment Routes — /api/v1/admin/assignments
|--------------------------------------------------------------------------
|
| New endpoints serving the question-based assignment workflow defined by
| the 2026 Figma redesign. The legacy file-based endpoints in
| routes/apis/assignments.php remain untouched and continue to serve the
| learner-facing API.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {

    // Lookup endpoints (declared before the resource routes so the URI
    // segments don't get matched as integer ids).
    Route::get('assignments/summary',         [AdminAssignmentController::class, 'summary']);
    Route::get('assignments/list',            [AdminAssignmentController::class, 'listMinimal']);
    Route::get('assignments/cohorts',         [AdminAssignmentController::class, 'cohorts']);
    Route::get('assignments/instructors',     [AdminAssignmentController::class, 'instructors']);
    Route::get('assignments/submissions',     [AdminAssignmentController::class, 'submissions']);
    Route::get('assignments/submissions/{submission}', [AdminAssignmentController::class, 'showSubmission']);
    Route::put('assignments/submissions/{submission}/answers/{answer}/grade',
        [AdminAssignmentController::class, 'gradeAnswer']);

    // Assignment resource
    Route::get('assignments',                 [AdminAssignmentController::class, 'index']);
    Route::post('assignments',                [AdminAssignmentController::class, 'store']);
    Route::get('assignments/{assignment}',    [AdminAssignmentController::class, 'show']);
    Route::put('assignments/{assignment}',    [AdminAssignmentController::class, 'update']);
    Route::delete('assignments/{assignment}', [AdminAssignmentController::class, 'destroy']);
});
