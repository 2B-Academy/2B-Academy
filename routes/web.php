<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FrontControllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| API Documentation (legacy aliases)
|--------------------------------------------------------------------------
| The canonical endpoints are provided by L5-Swagger:
|   - /api/documentation   → Swagger UI
|   - /docs                → OpenAPI JSON spec
| These aliases preserve the previously-published URLs.
*/
Route::redirect('/api/docs', '/api/documentation');
Route::redirect('/storage/api-docs/openapi.yaml', '/docs');




Route::namespace('App\Http\Controllers\FrontControllers')->name('front.')->group(function (){

    //CMS
    Route::get('/', 'CMSController@home')->name('home');
    Route::get('/about-us', 'CMSController@about')->name('about');
    Route::get('/instructors', 'CMSController@instructors')->name('instructors');
    Route::get('/articles', 'CMSController@articles')->name('articles');
    Route::get('/article/{id}/{slug}', 'CMSController@articleDetails')->name('articles.details');
    Route::get('/contact-us', 'CMSController@contact')->name('contact');
    Route::post('/store-contact-form', 'CMSController@submitContact')->name('contact.submit');

    //Courses
    Route::get('/courses', 'CourseController@courses')->name('courses');
    Route::get('/course/{id}/{slug}', 'CourseController@courseDetails')->name('course.details');

    //forms
    Route::middleware(['auth:web'])->group(function (){
        Route::get('/exam/{form_uuid}', 'FormController@index')->name('forms.start');
        Route::post('/exam/answers/{form_uuid}', 'FormController@saveExam')->name('forms.user.saveExam');
    });


    //Attendance QR Code
    Route::get('/2b/attendance', 'AttendanceController@form')->name('attendances.form');
    Route::get('/2b/attendance/getUser', 'AttendanceController@getUser')->name('attendances.getUser');
    Route::post('/2b/attendance/store', 'AttendanceController@store')->name('attendances.store');



    Route::middleware(['auth:web'])->group(function (){
        Route::post('/course/{course}/rating', 'CourseController@rating')->name('course.rating');
        Route::get('/course/{course_id}/lecture/{lecture_id}', 'CourseController@lecture')->name('course.lecture');
        Route::get('/course/{course_id}/exam/{exam_id}', 'CourseController@exam')->name('course.exam');
        Route::post('/course/{course}/lecture/{lecture}/question', 'CourseController@addLectureQuestion')->name('course.lecture.addQuestion');
        Route::post('/course/{course}/exam/{exam}/submit', 'CourseController@submitCourseExam')->name('course.exam.submit');
        Route::get('/storage/{filename}', [CourseController::class, 'stream'])->name('course.video.stream');
        Route::post('/course/lecture/progress', 'CourseController@progress')->name('course.lecture.progress');
    });
});
include __DIR__.'/auth.php';
include __DIR__.'/test.php';

/*
|--------------------------------------------------------------------------
| Public storage fallback
|--------------------------------------------------------------------------
| Files uploaded via the `public` disk live in `storage/app/public/...`
| and are normally served through the `public/storage` symlink created
| by `php artisan storage:link`. That symlink is frequently missing on
| fresh deploys (or gets wiped on redeploy), which makes every uploaded
| image 404 — the request falls through to Laravel instead of being
| served statically (exactly the `/storage/Course/*.jpg` symptom).
|
| This route streams the file straight off the public disk so images
| render even when the symlink is absent. When the symlink DOES exist,
| Apache/Nginx serves the file directly and never reaches this route,
| so it's a zero-cost safety net. Registered LAST so it can't shadow
| the `/storage/api-docs/...` redirect or the single-segment video
| stream route above.
|
| Still run `php artisan storage:link` on the server for best perf —
| this is a fallback, not a replacement.
*/
Route::get('/storage/{path}', function (string $path) {
    abort_if(str_contains($path, '..'), 404);

    $disk = Storage::disk('public');
    abort_unless($disk->exists($path), 404);

    // BinaryFileResponse → correct MIME guess + HTTP range support
    // (so this also works for audio/video, not just images).
    return response()->file($disk->path($path));
})->where('path', '.*')->name('storage.public');
