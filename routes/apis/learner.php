<?php

use App\Http\Controllers\Api\Mobile\AcademyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Learner Web API — /api/v1/learner/*
|--------------------------------------------------------------------------
|
| Per-user endpoints for the browser learner website. These REUSE the mobile
| Academy service layer (AcademyService / EnrolmentService) so the browser gets
| the same flow-shaped catalogue payloads (availability predicate, scopes,
| next_cohort, CTA state, enrol) WITHOUT exposing the mobile S2S shared token.
|
| Difference from /mobile/*: auth is per-user Sanctum (auth.user + role:User),
| not the S2S (mobile.token + mobile.employee) model. The Academy controller
| resolves the learner purely via $request->user(), which Sanctum populates, so
| the same controller methods serve both surfaces unchanged.
|
*/
Route::middleware(['auth.user', 'role:User'])
    ->prefix('learner/academy')
    ->group(function () {
        Route::get('summary',               [AcademyController::class, 'summary']);
        Route::get('scopes',                [AcademyController::class, 'scopes']);
        Route::get('courses',               [AcademyController::class, 'courses']);
        Route::get('courses/{course}',      [AcademyController::class, 'show'])
            ->whereNumber('course');
        Route::post('courses/{course}/enrol', [AcademyController::class, 'enrol'])
            ->whereNumber('course');
        // GAP 4 — "Notify me for next cohort" intent storage (learner-web
        // only for now; see NAS-LMS-Website-Business-Flows.md GAP 4).
        Route::post('courses/{course}/notify-me', [AcademyController::class, 'notifyMe'])
            ->whereNumber('course');
    });
