<?php

use App\Http\Controllers\apis\LearnerQuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learner Rich Quiz Submission Routes
|--------------------------------------------------------------------------
|
| New, additive learner-facing surface for the 2026 rich-question quiz
| workflow (mcq/yes_no/open/reorder). The legacy MCQ-only endpoint remains
| untouched: POST courses/{course}/exams/{exam}/submit (UserExamController).
|
|   GET  courses/{course}/quizzes/{quiz}/take                          — ordered questions, resume position, prior answers
|   POST courses/{course}/quizzes/{quiz}/questions/{question}/answer   — grade + persist one answer (auto-finalizes on last question)
|   POST courses/{course}/quizzes/{quiz}/finish                        — explicit finalize
|   GET  courses/{course}/quizzes/{quiz}/results                       — per-question breakdown + overall score/pass-fail
*/

Route::middleware(['auth.user', 'role:User'])->prefix('courses/{course}')->group(function () {
    Route::get('quizzes/{quiz}/take', [LearnerQuizController::class, 'take']);
    Route::post('quizzes/{quiz}/questions/{question}/answer', [LearnerQuizController::class, 'answerQuestion']);
    Route::post('quizzes/{quiz}/finish', [LearnerQuizController::class, 'finish']);
    Route::get('quizzes/{quiz}/results', [LearnerQuizController::class, 'results']);
});
