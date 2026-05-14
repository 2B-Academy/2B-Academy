<?php

use App\Http\Controllers\apis\InstructorController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Instructor Routes — /api/v1/instructors/*
|--------------------------------------------------------------------------
*/

// Public: all instructors list for course creation form
Route::get('instructors/all', [InstructorController::class, 'all']);

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('instructors',                  [InstructorController::class, 'index']);
    Route::get('instructors/{instructor}',     [InstructorController::class, 'show']);
    Route::post('instructors',                 [InstructorController::class, 'store']);
    Route::put('instructors/{instructor}',     [InstructorController::class, 'update']);
    Route::delete('instructors/{instructor}',  [InstructorController::class, 'destroy']);
});
