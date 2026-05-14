<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\FrontControllers\CourseController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

use Illuminate\Support\Facades\File;

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
