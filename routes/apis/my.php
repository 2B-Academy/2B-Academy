<?php

use App\Http\Controllers\apis\LectureProgressController;
use App\Http\Controllers\apis\UserCourseEvaluationController;
use App\Http\Controllers\apis\UserDashboardController;
use App\Http\Controllers\apis\UserExamController;
use App\Http\Controllers\apis\UserFormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Self-Service Routes  (all require auth.user + role:User)
|--------------------------------------------------------------------------
|
| Dashboard
|   GET  /my/dashboard           — personal stats
|   GET  /my/courses             — enrolled + public courses
|   GET  /my/exams               — exam history
|   GET  /my/exams/{id}          — exam result with answer breakdown
|   GET  /my/assignments         — assignments + submission status
|   GET  /my/certificates        — earned certificates
|   GET  /my/progress/{course}   — course completion % + per-lecture detail
|
| Exam Submission
|   POST /courses/{course}/exams/{exam}/submit  — submit exam answers (auto-graded)
|
| Lecture Progress
|   POST /courses/{course}/lectures/{lecture}/progress  — report watch progress
|
| Course Evaluation
|   GET  /courses/{course}/evaluate   — load evaluation form + check already-done
|   POST /courses/{course}/evaluate   — submit evaluation (1 per user per course)
|
| Forms (User submission)
|   GET  /forms/{uuid}/start   — start / resume form session
|   POST /forms/{uuid}/submit  — submit form answers (auto-graded)
|
*/

Route::middleware(['auth.user', 'role:User'])->group(function () {

    // ── My Dashboard ──────────────────────────────────────────────────────
    Route::prefix('my')->group(function () {
        Route::get('dashboard',           [UserDashboardController::class, 'dashboard']);
        Route::get('courses',             [UserDashboardController::class, 'myCourses']);
        Route::get('exams',               [UserDashboardController::class, 'myExams']);
        Route::get('exams/{id}',          [UserDashboardController::class, 'myExam']);
        Route::get('assignments',         [UserDashboardController::class, 'myAssignments']);
        Route::get('certificates',        [UserDashboardController::class, 'myCertificates']);
        Route::get('progress/{courseId}', [UserDashboardController::class, 'myProgress']);
        Route::get('ratings',             [UserDashboardController::class, 'myRatings']);
        Route::get('lecture-questions',   [UserDashboardController::class, 'myLectureQuestions']);
    });

    // ── Exam Submission ────────────────────────────────────────────────────
    Route::post('courses/{course}/exams/{exam}/submit', [UserExamController::class, 'submit']);

    // ── Lecture Progress ───────────────────────────────────────────────────
    Route::post('courses/{course}/lectures/{lecture}/progress', [LectureProgressController::class, 'store']);
    Route::get('courses/{course}/my-progress',                  [LectureProgressController::class, 'show']);

    // ── Course Evaluation ──────────────────────────────────────────────────
    Route::get('courses/{course}/evaluate',  [UserCourseEvaluationController::class, 'show']);
    Route::post('courses/{course}/evaluate', [UserCourseEvaluationController::class, 'store']);

    // ── Form Submission ────────────────────────────────────────────────────
    Route::get('forms/{formUuid}/start',   [UserFormController::class, 'start']);
    Route::post('forms/{formUuid}/submit', [UserFormController::class, 'submit']);
});
