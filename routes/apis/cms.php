<?php

use App\Http\Controllers\apis\CmsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| CMS Routes — /api/v1/about & /api/v1/testimonials
|--------------------------------------------------------------------------
*/

// Public
Route::get('about',                    [CmsController::class, 'aboutShow']);
Route::get('testimonials/active',      [CmsController::class, 'testimonialActiveList']);
Route::get('testimonials',             [CmsController::class, 'testimonialIndex']);
Route::get('testimonials/{testimonial}', [CmsController::class, 'testimonialShow']);

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::post('about',                   [CmsController::class, 'aboutUpdate']);

    Route::post('testimonials',                        [CmsController::class, 'testimonialStore']);
    Route::put('testimonials/{testimonial}',           [CmsController::class, 'testimonialUpdate']);
    Route::delete('testimonials/{testimonial}',        [CmsController::class, 'testimonialDestroy']);
});
