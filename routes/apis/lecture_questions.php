<?php

use App\Http\Controllers\apis\CourseLectureQuestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Lecture Question Routes
|--------------------------------------------------------------------------
| Admin : GET  /api/v1/lecture-questions (filtered list)
|         PUT  /api/v1/lecture-questions/{question}/answer
|         DELETE /api/v1/lecture-questions/{question}
| User  : POST /api/v1/courses/{course}/lectures/{lecture}/questions
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('lecture-questions',                              [CourseLectureQuestionController::class, 'index']);
    Route::put('lecture-questions/{question}/answer',            [CourseLectureQuestionController::class, 'answer']);
    Route::delete('lecture-questions/{question}',                [CourseLectureQuestionController::class, 'destroy']);
});

Route::middleware(['auth.user', 'role:User'])->group(function () {
    Route::post('courses/{course}/lectures/{lecture}/questions', [CourseLectureQuestionController::class, 'store']);
});
