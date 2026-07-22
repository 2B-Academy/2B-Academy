<?php

use App\Http\Controllers\Api\Mobile\AcademyController;
use App\Http\Controllers\Api\Mobile\CertificateController;
use App\Http\Controllers\Api\Mobile\MyLearningController;
use App\Http\Controllers\apis\Learner\ProfileController;
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
Route::prefix('learner/academy')->group(function () {
    // Public catalogue browse: optional auth. A guest sees only `for_public`
    // (General) courses; an authenticated learner gets the personalised
    // All / Special / General split. Same controller methods either way —
    // they resolve the learner via $request->user(), which is simply null
    // for a guest.
    Route::middleware('auth.user.optional')->group(function () {
        Route::get('scopes',           [AcademyController::class, 'scopes']);
        Route::get('courses',          [AcademyController::class, 'courses']);
        Route::get('job-role-filters', [AcademyController::class, 'jobRoleFilters']);
        // Detail view is public too: a guest can open a course page and gets a
        // guest-shaped CTA (EnrolNow/GetNotified) — enrolment itself still
        // requires login (the frontend shows a login prompt).
        Route::get('courses/{course}', [AcademyController::class, 'show'])
            ->whereNumber('course');
    });

    // Authenticated learner only — enrolment and notify-intent require an
    // identified user.
    Route::middleware(['auth.user', 'role:User'])->group(function () {
        Route::get('summary',          [AcademyController::class, 'summary']);
        Route::post('courses/{course}/enrol', [AcademyController::class, 'enrol'])
            ->whereNumber('course');
        // GAP 4 — "Notify me for next cohort" intent storage (learner-web
        // only for now; see NAS-LMS-Website-Business-Flows.md GAP 4).
        Route::post('courses/{course}/notify-me', [AcademyController::class, 'notifyMe'])
            ->whereNumber('course');
    });
});

/*
| Learner Profile dashboard — /api/v1/learner/profile/*
|
| The browser Profile screen (Qualifications + My Learnings dashboard). The
| header counters and the rich per-qualification earned/uncovered course
| breakdown are web-only projections owned by ProfileController; the
| My-Learning / certificate / rating shapes reuse the mobile controllers
| verbatim (identical service layer, resolved via $request->user()).
*/
Route::middleware(['auth.user', 'role:User'])
    ->prefix('learner/profile')
    ->group(function () {
        Route::get('summary',        [ProfileController::class, 'summary']);
        Route::get('qualifications', [ProfileController::class, 'qualifications']);
        Route::get('completed',      [ProfileController::class, 'completed']);

        Route::get('learnings',    [MyLearningController::class, 'active']);
        Route::get('certificates', [MyLearningController::class, 'certificates']);
        Route::get('courses/{course}/sessions', [MyLearningController::class, 'sessions'])
            ->whereNumber('course');
        Route::post('courses/{course}/rating',  [MyLearningController::class, 'submitRating'])
            ->whereNumber('course');
        Route::get('certificates/{certificate}/download', [CertificateController::class, 'download'])
            ->whereNumber('certificate');
    });
