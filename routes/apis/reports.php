<?php

use App\Http\Controllers\apis\ReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Report Routes — /api/v1/reports/*
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('reports')->group(function () {
    Route::get('compliance-by-job-title', [ReportController::class, 'complianceByJobTitle']);
    Route::get('individual-compliance',   [ReportController::class, 'individualCompliance']);
    Route::get('attendance',              [ReportController::class, 'attendance']);
    Route::get('completion',              [ReportController::class, 'completion']);
    Route::get('scores',                  [ReportController::class, 'scores']);
    Route::get('certificate-status',      [ReportController::class, 'certificateStatus']);
});
