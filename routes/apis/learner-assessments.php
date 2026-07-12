<?php

use App\Http\Controllers\apis\LearnerAssignmentController;
use App\Http\Controllers\apis\LearnerQuizController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learner Rich Quiz/Assignment Submission Routes
|--------------------------------------------------------------------------
|
| New, additive learner-facing surface for the 2026 rich-question workflow
| (mcq/yes_no/open/reorder). The legacy MCQ-only endpoints remain untouched:
|   - POST courses/{course}/exams/{exam}/submit             (UserExamController)
|   - POST courses/{course}/assignments/{assignment}/submit (CourseAssignmentController, file upload)
|
| Quiz flow:
|   GET  courses/{course}/quizzes/{quiz}/take                          — ordered questions, resume position, prior answers
|   POST courses/{course}/quizzes/{quiz}/questions/{question}/answer   — grade + persist one answer (auto-finalizes on last question)
|   POST courses/{course}/quizzes/{quiz}/finish                        — explicit finalize
|   GET  courses/{course}/quizzes/{quiz}/results                       — per-question breakdown + overall score/pass-fail
|
| Assignment flow (question-based assignments only — file-based assignments
| keep using the legacy /submit endpoint):
|   GET  courses/{course}/assignments/{assignment}/take
|   POST courses/{course}/assignments/{assignment}/questions/{question}/answer
|   POST courses/{course}/assignments/{assignment}/finish
|   GET  courses/{course}/assignments/{assignment}/results
*/

Route::middleware(['auth.user', 'role:User'])->prefix('courses/{course}')->group(function () {
    Route::get('quizzes/{quiz}/take', [LearnerQuizController::class, 'take']);
    Route::post('quizzes/{quiz}/questions/{question}/answer', [LearnerQuizController::class, 'answerQuestion']);
    Route::post('quizzes/{quiz}/finish', [LearnerQuizController::class, 'finish']);
    Route::get('quizzes/{quiz}/results', [LearnerQuizController::class, 'results']);

    Route::get('assignments/{assignment}/take', [LearnerAssignmentController::class, 'take']);
    Route::post('assignments/{assignment}/questions/{question}/answer', [LearnerAssignmentController::class, 'answerQuestion']);
    Route::post('assignments/{assignment}/finish', [LearnerAssignmentController::class, 'finish']);
    Route::get('assignments/{assignment}/results', [LearnerAssignmentController::class, 'results']);
});
