<?php

use App\Http\Controllers\apis\EvaluationCategoryController;
use App\Http\Controllers\apis\EvaluationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Evaluation Routes — /api/v1/evaluation-categories & /api/v1/evaluations
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {

    // Evaluation categories
    Route::get('evaluation-categories/all',                    [EvaluationCategoryController::class, 'all']);
    Route::get('evaluation-categories',                        [EvaluationCategoryController::class, 'index']);
    Route::get('evaluation-categories/{evaluationCategory}',   [EvaluationCategoryController::class, 'show']);
    Route::post('evaluation-categories',                       [EvaluationCategoryController::class, 'store']);
    Route::put('evaluation-categories/{evaluationCategory}',   [EvaluationCategoryController::class, 'update']);
    Route::delete('evaluation-categories/{evaluationCategory}',[EvaluationCategoryController::class, 'destroy']);

    // Evaluations
    Route::get('evaluations',              [EvaluationController::class, 'index']);
    Route::get('evaluations/{evaluation}', [EvaluationController::class, 'show']);
    Route::post('evaluations',             [EvaluationController::class, 'store']);
    Route::put('evaluations/{evaluation}', [EvaluationController::class, 'update']);
    Route::delete('evaluations/{evaluation}', [EvaluationController::class, 'destroy']);
});
