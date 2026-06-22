<?php

use App\Http\Controllers\apis\Admin\AdminControllersController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin Controllers Routes — /api/v1/admin/controllers
|--------------------------------------------------------------------------
|
| New endpoints serving the 2026 Controllers redesign. The legacy
| public endpoints (routes/apis/admins.php → /api/v1/admins) remain
| untouched and continue to serve their original consumers.
*/

Route::middleware(['auth.user', 'role:Admin'])->prefix('admin')->group(function () {
    Route::get('controllers',            [AdminControllersController::class, 'index']);
    Route::post('controllers',           [AdminControllersController::class, 'store']);
    Route::get('controllers/{admin}',    [AdminControllersController::class, 'show'])->whereNumber('admin');
    Route::put('controllers/{admin}',    [AdminControllersController::class, 'update'])->whereNumber('admin');
    Route::delete('controllers/{admin}', [AdminControllersController::class, 'destroy'])->whereNumber('admin');
});
