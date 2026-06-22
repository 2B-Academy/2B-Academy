<?php

use App\Http\Controllers\admin\SessionPasscodeController;
use App\Http\Controllers\Api\Mobile\AcademyController;
use App\Http\Controllers\Api\Mobile\AttendanceController;
use App\Http\Controllers\Api\Mobile\CertificateController;
use App\Http\Controllers\Api\Mobile\MyLearningController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| 📱 Mobile API Routes — /api/v1/mobile/*
|--------------------------------------------------------------------------
|
| Implements the NAS-LMS Mobile contract (S-01 → S-07). The intended
| consumer is the HR system, which acts on behalf of each employee
| in a server-to-server integration.
|
| Authentication model (S2S, NOT per-user Sanctum tokens):
|
|   1. `mobile.token`     — every request must present
|                            `Authorization: Bearer <static_token>`
|                            where the value comes from settings
|                            (`mobile_shared_bearer_token`).
|   2. `mobile.employee`  — every request must present
|                            `Employee-Code: <machine_code>`. The
|                            middleware loads the matching User and
|                            wires it into the request so every
|                            `$request->user()` call inside services /
|                            resources keeps working.
|
| The `SetLocale` middleware (registered on the api group in
| bootstrap/app.php) resolves the locale from `Accept-Language`
| (ar | en, default ar) so translatable response fields come back
| localized automatically.
|
| The admin-side passcode lifecycle (issue / revoke a session passcode)
| lives under /api/v1/admin/course-sessions/{session}/passcode and is
| still guarded by Sanctum + role:Admin (admin-facing, not mobile).
|
*/

// ───────────────────────────────────────────────────────────────────────
// Mobile S2S routes — HR integration acts on behalf of each employee
// ───────────────────────────────────────────────────────────────────────
Route::middleware(['mobile.token', 'mobile.employee'])
    ->prefix('mobile')
    ->group(function () {

        // ── Identity ──────────────────────────────────────────────────
        Route::get('me', [MyLearningController::class, 'me']);

        // ── S-01 → S-04 · Academy ────────────────────────────────────
        Route::prefix('academy')->group(function () {
            Route::get('summary',                            [AcademyController::class, 'summary']);
            Route::get('scopes',                             [AcademyController::class, 'scopes']);
            Route::get('courses',                            [AcademyController::class, 'courses']);
            Route::get('courses/{course}',                   [AcademyController::class, 'show'])
                ->whereNumber('course');
            Route::post('courses/{course}/enrol',            [AcademyController::class, 'enrol'])
                ->whereNumber('course');
        });

        // ── S-05 + S-07 list · My Learning ───────────────────────────
        Route::prefix('my-learning')->group(function () {
            Route::get('overview',                                [MyLearningController::class, 'overview']);
            Route::get('active',                                  [MyLearningController::class, 'active']);
            Route::get('qualifications',                          [MyLearningController::class, 'qualifications']);
            Route::get('certificates',                            [MyLearningController::class, 'certificates']);
            Route::get('courses/{course}/sessions',               [MyLearningController::class, 'sessions'])
                ->whereNumber('course');
            Route::post('courses/{course}/rating',                [MyLearningController::class, 'submitRating'])
                ->whereNumber('course');
        });

        // ── S-06 · Mark Present (passcode flow) ──────────────────────
        Route::post('attendance/mark',                   [AttendanceController::class, 'mark']);

        // ── S-07 · Certificate detail + download ─────────────────────
        // Certificates are first-class entities — looked up by their own
        // integer id (scoped to the learner). No more compound ids.
        Route::prefix('certificates')->group(function () {
            Route::get('{certificateId}',          [CertificateController::class, 'show'])
                ->whereNumber('certificateId');
            Route::get('{certificateId}/download', [CertificateController::class, 'download'])
                ->whereNumber('certificateId');
        });
    });

// ───────────────────────────────────────────────────────────────────────
// Mobile-flow admin support — instructors / admins issue & revoke
// the session passcode that powers the mobile S-06 Mark Present screen.
// ───────────────────────────────────────────────────────────────────────
Route::middleware(['auth.user', 'role:Admin'])
    ->prefix('admin/course-sessions')
    ->group(function () {
        Route::post('{session}/passcode',   [SessionPasscodeController::class, 'issue'])
            ->whereNumber('session');
        Route::delete('{session}/passcode', [SessionPasscodeController::class, 'revoke'])
            ->whereNumber('session');
    });
