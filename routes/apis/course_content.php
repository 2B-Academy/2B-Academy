<?php

use App\Http\Controllers\apis\CourseExamController;
use App\Http\Controllers\apis\CourseLectureController;
use App\Http\Controllers\apis\CourseSectionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Course Content Routes — /api/v1/courses/{course}/sections|lectures|exams
|--------------------------------------------------------------------------
*/

Route::middleware('auth.user')->group(function () {

    // Sections — read
    Route::get('courses/{course}/sections', [CourseSectionController::class, 'index']);

    // Lectures — read (grouped by section)
    Route::get('courses/{course}/lectures', [CourseLectureController::class, 'index']);

    // Modules — flat list (powers admin "Content" tab in course detail)
    Route::get('courses/{course}/modules',  [CourseLectureController::class, 'indexFlat']);

    // Exams — read
    Route::get('courses/{course}/exams',         [CourseExamController::class, 'index']);
    Route::get('courses/{course}/exams/{exam}',  [CourseExamController::class, 'show']);
});

Route::middleware(['auth.user', 'role:Admin'])->group(function () {

    // Sections — write
    Route::post('courses/{course}/sections',               [CourseSectionController::class, 'store']);
    Route::post('courses/{course}/sections/sync',          [CourseSectionController::class, 'sync']);
    Route::put('courses/{course}/sections/{section}',      [CourseSectionController::class, 'update']);
    Route::delete('courses/{course}/sections/{section}',   [CourseSectionController::class, 'destroy']);

    // Lectures — write
    // NB: /lectures/upload must come BEFORE /{lecture} to avoid route binding.
    Route::post('courses/{course}/lectures/upload',        [CourseLectureController::class, 'uploadFile']);
    Route::post('courses/{course}/lectures',               [CourseLectureController::class, 'store']);
    Route::put('courses/{course}/lectures/{lecture}',      [CourseLectureController::class, 'update']);
    Route::delete('courses/{course}/lectures/{lecture}',   [CourseLectureController::class, 'destroy']);

    // Exams — write
    Route::post('courses/{course}/exams',                  [CourseExamController::class, 'store']);
    Route::put('courses/{course}/exams/{exam}',            [CourseExamController::class, 'update']);
    Route::delete('courses/{course}/exams/{exam}',         [CourseExamController::class, 'destroy']);
});
