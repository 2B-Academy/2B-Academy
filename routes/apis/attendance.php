<?php

use App\Http\Controllers\apis\AttendanceController;
use App\Http\Controllers\apis\CohortAttendanceController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Attendance Routes — /api/v1/attendance
|--------------------------------------------------------------------------
| Admin: GET  /api/v1/attendance                                         — paginated list with filters
|        POST /api/v1/attendance                                         — record (status=1) or remove (status=0)
|        GET  /api/v1/courses/{course}/cohorts/{cohort}/attendance       — full cohort rollup for the drawer
*/

Route::middleware(['auth.user', 'role:Admin'])->group(function () {
    Route::get('attendance',  [AttendanceController::class, 'index']);
    Route::post('attendance', [AttendanceController::class, 'store']);

    // Drives the "Attendance Record" drawer on the course detail screen.
    // `{cohort}` is a course_sections.id; the controller enforces the
    // parent-child relationship so a cohort from another course 404s.
    Route::get('courses/{course}/cohorts/{cohort}/attendance', [CohortAttendanceController::class, 'show']);
});

Route::middleware(['auth.user', 'role:User'])->group(function () {
    Route::get('courses/{course}/my-attendance', [AttendanceController::class, 'myCourseAttendance']);
});
