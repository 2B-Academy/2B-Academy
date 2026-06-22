<?php

use App\Http\Controllers\AdminControllers\AbsenceController;
use App\Http\Controllers\AdminControllers\AttendanceController;
use App\Http\Controllers\AdminControllers\AuthController;
use App\Http\Controllers\AdminControllers\CourseAssignmentController;
use App\Http\Controllers\AdminControllers\CourseLectureController;
use App\Http\Controllers\AdminControllers\CourseResourceController;
use App\Http\Controllers\AdminControllers\CourseSectionController;
use App\Http\Controllers\AdminControllers\EvaluationReportController;
use App\Http\Controllers\AdminControllers\FormController;
use App\Http\Controllers\AdminControllers\ServiceController;
use App\Http\Controllers\AdminControllers\UserCertificateController;
use App\Http\Controllers\AdminControllers\UserController;
use App\Http\Controllers\AdminControllers\UserCourseController;
use App\Http\Controllers\AdminControllers\UserCourseOfflineController;
use App\Http\Controllers\AdminControllers\UserCourseProgressController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminControllers\DashboardController;


Route::get('/login', [AuthController::class, 'login_page'])->name('admin.login_page');
Route::post('/login', [AuthController::class, 'login'])->name('admin.login');

Route::get("switch-language/{lang}", function ($lang) {
    app()->setLocale($lang);
    session()->put('locale', $lang);
    return redirect()->back();
})->name('switch-language');


/*All Admin Routes List*/
Route::middleware(['auth:admin'])->namespace('App\Http\Controllers\AdminControllers')->name('admin.')->group(function () {

    //Default
    Route::post('/logout', [DashboardController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::post('/quickChange', [DashboardController::class, 'quickChange'])->name('quickChange');
    Route::post('/deleteSelectedItems', [DashboardController::class, 'deleteSelectedItems'])->name('deleteSelectedItems');
    Route::get('/profile', [DashboardController::class, 'userProfile'])->name('userProfile');
    Route::put('/profile', [DashboardController::class, 'updateUserProfile'])->name('updateUserProfile');
    Route::post('/sync-employees-job', [DashboardController::class, 'syncEmployeesJob'])->name('sync-employees-job');
    Route::get('/users/sync', [UserController::class, 'syncEmployees'])->name('users.sync');
    Route::resource('users', 'UserController');
    Route::delete('/course/rating/{id}', [UserController::class, 'destroyRating'])->name('user.ratings.destroy');
    Route::post('/course/lecture-question/{id}', [UserController::class, 'addAnswerLectureQuestion'])->name('user.lecture-question.update');
    Route::delete('/course/lecture-question/{id}', [UserController::class, 'destroyLectureQuestion'])->name('user.lecture-question.destroy');
    Route::delete('/course/user-exam/{id}', [UserController::class, 'destroyUserExam'])->name('user.user-exam.destroy');
    Route::resource('categories', 'CategoryController');
    Route::resource('instructors', 'InstructorController');
    Route::resource('qualification-skills', 'QualificationSkillController');
    Route::resource('courses', 'CourseController');
    Route::resource('courses.sections', 'CourseSectionController');
    Route::delete('/course/{course}/sections', [CourseSectionController::class, 'destroyAll'])->name('courses.sections.destroyAll');
    Route::resource('courses.resources', 'CourseResourceController');
    Route::delete('/course/{course}/resources', [CourseResourceController::class, 'destroyAll'])->name('courses.resources.destroyAll');
    Route::resource('courses.assignments', 'CourseAssignmentController');
    Route::delete('/course/{course}/assignments', [CourseAssignmentController::class, 'destroyAll'])->name('courses.assignments.destroyAll');
    Route::resource('courses.lectures', 'CourseLectureController');
    Route::resource('courses.sessions', 'CourseSessionController');
    Route::resource('courses.exams', 'CourseExamController');
    Route::resource('users-courses', 'UserCourseController');
    Route::resource('users-courses-offline', 'UserCourseOfflineController');
    Route::get('/course/groups', [UserCourseOfflineController::class, 'courseGroups'])->name('get-groups-of-course');


    Route::get('/users-courses-progress-export', [UserCourseProgressController::class, 'export'])->name('users.courses.progress.export');
    Route::get('/certificates/download-all', [UserCertificateController::class, 'downloadAll'])->name('certificates.downloadAll');
    Route::get('/certificates/show', [UserCertificateController::class, 'showCertificate'])->name('certificates.showCertificate');
    Route::resource('certificates', 'UserCertificateController');
    Route::resource('users-courses-progress', 'UserCourseProgressController');
    Route::resource('users-courses-ratings', 'UserCourseRatingController');
    Route::resource('users-lectures-questions', 'UserLectureQuestionController');
    Route::resource('users-courses-assignments', 'UserCourseAssignmentController');
    Route::resource('abouts', 'AboutController');
    Route::resource('blogs', 'BlogController');
    Route::resource('testimonials', 'TestimonialController');
    Route::resource('settings', 'SettingController');
    Route::resource('contacts', 'ContactController');
    Route::resource('admins', 'AdminController');
    Route::resource('roles', 'RoleController');

    Route::resource('notifications', 'NotificationController');

    Route::resource('videos', 'VideoController');

    //forms
    Route::get('/forms/export/{form}', [FormController::class, 'export'])->name('forms.export');
    Route::get('/forms/export/questions/{form}', [FormController::class, 'exportMostQuestions'])->name('forms.export.questions');
    Route::get('/forms/export/questions/text/{form}', [FormController::class, 'exportTextQuestions'])->name('forms.export.questions.text');
    Route::get('/forms/export/questions/wrong/{form}', [FormController::class, 'exportWrongQuestions'])->name('forms.export.questions.wrong');
    Route::delete('/forms/question/destroy/{question}', [FormController::class, 'destroyQuestion'])->name('forms.question.destroy');
    Route::resource('forms', 'FormController');



    //Evaluations
    Route::resource('evaluation-categories', 'EvaluationCategoryController');
    Route::resource('evaluations', 'EvaluationController');

    //Evaluations Reports
    Route::get('/evaluations-report-per-question', [EvaluationReportController::class, 'export_per_questions'])->name('evaluations-reports.export.per_question');
    Route::get('/evaluations-report-per-category', [EvaluationReportController::class, 'export_per_category'])->name('evaluations-reports.export.per_category');
    Route::get('/evaluations-report-per-text', [EvaluationReportController::class, 'export_per_text'])->name('evaluations-reports.export.per_text');
    Route::resource('evaluations-reports', 'EvaluationReportController');


    //Attendances

    Route::get('/compare-attendance-dates', [AttendanceController::class, 'compareDates'])->name('attendances.compare-attendance-dates');
    Route::get('/getUserCourses', [AttendanceController::class, 'getUserCourses'])->name('attendances.getUserCourses');
    Route::get('/attendance/qr', [AttendanceController::class, 'qr'])->name('attendances.qr');
    Route::get('/attendances/export/{type}', [AttendanceController::class, 'export'])->name('attendances.export');
    Route::resource('attendances', 'AttendanceController');

    //Absences
    Route::get('/absences/export', [AbsenceController::class, 'export'])->name('absences.export');
    Route::resource('absences', 'AbsenceController');


    //Tiny MCE upload images
    Route::post('/upload-tiny-file', [DashboardController::class, 'uploadTinyFile'])->name('upload.tiny');


    //Choose from files
    Route::get('/videos/list', [DashboardController::class, 'videosListFromStorage'])->name('videosListFromStorage');

    //get users from ajax
    Route::get('/users/ajax/select', [UserController::class, 'getUsers'])->name('ajax.users');


});
