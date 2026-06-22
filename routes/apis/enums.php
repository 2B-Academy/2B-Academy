<?php

use App\Http\Controllers\apis\EnumController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Enum Routes — /api/v1/enums/*
|--------------------------------------------------------------------------
| Public reference data — used by every frontend dropdown. Honors
| `Accept-Language: en|ar`. See `EnumController` for full Swagger docs.
*/

/**
 * Public dropdown reference data. The actual handler is generic — it reads
 * the trailing path segment and looks the enum up in `EnumRegistry`. Each
 * enum is documented as its own endpoint in {@see \App\OpenApi\EnumEndpoints}
 * so Swagger UI shows one named entry per enum (course_type, cohort_status,
 * etc.) instead of one anonymous `/enums/{name}` row.
 */
Route::prefix('enums')->group(function () {
    Route::get('/',       [EnumController::class, 'index']);
    Route::get('/{name}', [EnumController::class, 'show']);
});
