<?php

use App\Http\Controllers\apis\ContactController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Contact / Book-a-Demo Routes — /api/v1
|--------------------------------------------------------------------------
| Public: the "Request a Demo" form and the quick-contact details. The
| submit endpoint is throttled to deter spam.
*/

Route::get('contact/info', [ContactController::class, 'info']);
Route::post('contact', [ContactController::class, 'store'])->middleware('throttle:20,1');
