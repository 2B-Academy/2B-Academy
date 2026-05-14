<?php

use App\Http\Controllers\apis\UserCourseProgressController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User Course Progress Routes — /api/v1/progress
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('progress', [UserCourseProgressController::class, 'index']);
});
