<?php

use App\Http\Controllers\apis\FormController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Form Routes — /api/v1/forms
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('forms',              [FormController::class, 'index']);
    Route::get('forms/{form}',       [FormController::class, 'show']);
    Route::post('forms',             [FormController::class, 'store']);
    Route::put('forms/{form}',       [FormController::class, 'update']);
    Route::delete('forms/{form}',    [FormController::class, 'destroy']);

    // Question management
    Route::post('forms/{form}/questions',                      [FormController::class, 'addQuestion']);
    Route::delete('forms/{form}/questions/{question}',         [FormController::class, 'destroyQuestion']);
});
