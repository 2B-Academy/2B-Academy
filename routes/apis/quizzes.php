<?php

use App\Http\Controllers\apis\QuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Quiz / User Exam Routes — /api/v1/quizzes/*
|--------------------------------------------------------------------------
*/

// Admin only — browse all quiz attempts
Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('quizzes',         [QuizController::class, 'index']);
    Route::get('quizzes/{userExam}', [QuizController::class, 'show']);
});
