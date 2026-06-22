<?php

use App\Http\Controllers\apis\RoleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Role Routes — /api/v1/roles
|--------------------------------------------------------------------------
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('permissions',     [RoleController::class, 'permissions']);
    Route::get('roles/all',       [RoleController::class, 'all']);
    Route::get('roles',           [RoleController::class, 'index']);
    Route::get('roles/{role}',    [RoleController::class, 'show']);
    Route::post('roles',          [RoleController::class, 'store']);
    Route::put('roles/{role}',    [RoleController::class, 'update']);
    Route::delete('roles/{role}', [RoleController::class, 'destroy']);
});
