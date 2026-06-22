<?php

use App\Http\Controllers\apis\Admin\AdminReportController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Reports Routes — /api/v1/admin/reports
|--------------------------------------------------------------------------
|
| New endpoints powering the 2026 Reports redesign (summary cards + live
| compliance preview + CSV / XLSX exports + bundled Export All). The
| legacy endpoints in routes/apis/reports.php remain untouched.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {

    Route::get('reports/summary',             [AdminReportController::class, 'summary']);
    Route::get('reports/compliance-preview',  [AdminReportController::class, 'compliancePreview']);
    Route::get('reports/export-all',          [AdminReportController::class, 'exportAll']);

    // Per-report export (declared last so /export-all & lookup endpoints
    // resolve first).
    Route::get('reports/{type}/export',       [AdminReportController::class, 'export']);
});
