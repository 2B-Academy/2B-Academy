<?php

use App\Http\Controllers\apis\Admin\AdminRoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Roles Routes — /api/v1/admin/roles
|--------------------------------------------------------------------------
|
| New endpoints serving the 2026 admin Roles redesign. The legacy public
| endpoints (routes/apis/roles.php → /api/v1/roles, /api/v1/permissions)
| remain untouched and continue to serve their original consumers.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('roles/sections', [AdminRoleController::class, 'sections']);

    Route::get('roles',           [AdminRoleController::class, 'index']);
    Route::post('roles',          [AdminRoleController::class, 'store']);
    Route::get('roles/{id}',      [AdminRoleController::class, 'show'])->whereNumber('id');
    Route::put('roles/{id}',      [AdminRoleController::class, 'update'])->whereNumber('id');
    Route::delete('roles/{id}',   [AdminRoleController::class, 'destroy'])->whereNumber('id');
});
