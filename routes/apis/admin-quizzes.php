<?php

use App\Http\Controllers\apis\Admin\AdminQuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Rich-Quiz Routes — /api/v1/admin/quizzes
|--------------------------------------------------------------------------
|
| New endpoints serving the question-based quiz workflow defined by the
| 2026 Figma redesign. The legacy MCQ-only endpoints in
| routes/apis/quizzes.php (learner attempt listings) remain untouched and
| continue to serve the existing learner-facing API.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {

    // Lookup endpoints (declared before the resource routes so the URI
    // segments don't get matched as integer ids).
    Route::get('quizzes/summary',     [AdminQuizController::class, 'summary']);
    Route::get('quizzes/list',        [AdminQuizController::class, 'listMinimal']);
    Route::get('quizzes/cohorts',     [AdminQuizController::class, 'cohorts']);
    Route::get('quizzes/instructors', [AdminQuizController::class, 'instructors']);
    Route::get('quizzes/submissions', [AdminQuizController::class, 'submissions']);
    Route::get('quizzes/submissions/{submission}', [AdminQuizController::class, 'showSubmission']);
    Route::put('quizzes/submissions/{submission}/answers/{answer}/grade',
        [AdminQuizController::class, 'gradeAnswer']);

    // Quiz resource
    Route::get('quizzes',           [AdminQuizController::class, 'index']);
    Route::post('quizzes',          [AdminQuizController::class, 'store']);
    Route::get('quizzes/{quiz}',    [AdminQuizController::class, 'show']);
    Route::put('quizzes/{quiz}',    [AdminQuizController::class, 'update']);
    Route::delete('quizzes/{quiz}', [AdminQuizController::class, 'destroy']);
});
