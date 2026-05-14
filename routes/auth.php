<?php


use Illuminate\Support\Facades\Route;

Route::namespace('App\Http\Controllers\AuthControllers')->prefix('user')->name('front.auth.')->group(function () {
    Route::get('/login', 'LoginController@login')->name('login');
    Route::post('/login', 'LoginController@postLogin')->name('postLogin');

    Route::middleware(['auth:web'])->group(function (){
        Route::post('/logout', 'DashboardController@logout')->name('logout');
        Route::get('/dashboard', 'DashboardController@dashboard')->name('dashboard');
        Route::get('/my-courses', 'DashboardController@myCourses')->name('my-courses');
        Route::get('/my-ratings', 'DashboardController@myRatings')->name('my-ratings');
        Route::get('/my-lectures-questions', 'DashboardController@myLecturesQuestions')->name('my-lectures-questions');
        Route::get('/my-exams', 'DashboardController@myExams')->name('my-exams');
        Route::get('/my-exams/answers/{exam}', 'DashboardController@myExamAnswers')->name('exam-answers');
        Route::get('/my-assignments', 'DashboardController@myAssignments')->name('my-assignments');
        Route::post('/my-assignment/{id}', 'DashboardController@uploadAssignment')->name('upload-assignment');
        Route::get('/my-certificates', 'DashboardController@myCertificates')->name('my-certificates');
        Route::get('/my-certificates/certificate/{course}', 'DashboardController@myCertificate')->name('user-certificate');

        //Evaluation
        Route::get('/course/{course}/evaluation', 'EvaluationController@index')->name('course.evaluation');
        Route::post('/course/{course}/evaluation', 'EvaluationController@store')->name('course.evaluation.submit');

        //Attendances
        Route::get('/course/{course}/attendance', 'DashboardController@attendance')->name('courses.attendance');


    });

});
