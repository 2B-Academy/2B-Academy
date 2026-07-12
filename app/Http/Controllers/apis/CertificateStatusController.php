<?php

namespace App\Http\Controllers\apis;

use App\Models\Course;
use App\Services\CertificateProjectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Learner-facing certificate ELIGIBILITY PROJECTION — "Certificate: On
 * track / At risk / Blocked" — as distinct from the already-issued
 * certificate list (App\Http\Controllers\apis\CertificateController,
 * admin; UserDashboardController::myCertificates, learner). Backs the
 * course-player's persistent certificate status header badge.
 */
class CertificateStatusController extends ApiController
{
    public function __construct(private readonly CertificateProjectionService $projection) {}

    /** GET courses/{course}/certificate-status */
    public function show(Request $request, Course $course): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->projection->projectForCourse($request->user(), $course),
        );
    }
}
