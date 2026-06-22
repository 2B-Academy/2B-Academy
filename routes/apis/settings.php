<?php

use App\Http\Controllers\apis\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Settings Routes — /api/v1/settings
|--------------------------------------------------------------------------
| Public : GET /api/v1/settings          — key=>value map (no auth required)
| Admin  : GET /api/v1/admin/settings    — full list with type metadata
|          PUT /api/v1/admin/settings    — update settings
*/

Route::get('settings', [SettingController::class, 'index']);

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get ('admin/settings',         [SettingController::class, 'adminIndex']);
    Route::put ('admin/settings',         [SettingController::class, 'update']);
    Route::post('admin/settings/upload',  [SettingController::class, 'upload']);
});
