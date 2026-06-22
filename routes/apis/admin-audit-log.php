<?php

use App\Http\Controllers\apis\Admin\AdminAuditLogController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Audit Log Routes — /api/v1/admin/audit-log
|--------------------------------------------------------------------------
|
| New endpoints serving the platform-wide Audit Log overview defined by
| the 2026 Figma redesign. The legacy public endpoint
| (routes/apis/audit_log.php → GET /api/v1/audit-log) is left untouched
| and continues to serve its original consumers.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('audit-log/filter-options', [AdminAuditLogController::class, 'filterOptions']);
    Route::get('audit-log/export',         [AdminAuditLogController::class, 'export']);
    Route::get('audit-log',                [AdminAuditLogController::class, 'index']);
});
