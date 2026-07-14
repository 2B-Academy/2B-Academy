<?php

declare(strict_types=1);

namespace App\Http\Controllers\apis\Learner;

use App\Http\Controllers\apis\ApiController;
use App\Http\Resources\Mobile\LearnerIdentityResource;
use App\Services\Learner\ProfileDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * 🌐 LEARNER WEB — Profile dashboard composite endpoints.
 *
 * Same per-user Sanctum auth model as routes/apis/learner.php. The
 * straightforward My-Learning / certificate / rating shapes are mirrored
 * directly from the mobile controllers (identical service layer); this
 * controller owns the two web-only projections the browser Profile screen
 * layers on top: the header counters and the rich per-qualification
 * earned/uncovered course breakdown.
 */
final class ProfileController extends ApiController
{
    public function __construct(
        private readonly ProfileDashboardService $dashboard,
    ) {}

    /** Learner identity card + the four header counters. */
    public function summary(Request $request): JsonResponse
    {
        $user = $request->user()->loadMissing('jobTitle');

        return $this->success('Profile summary', [
            'learner' => new LearnerIdentityResource($user),
            'counts'  => $this->dashboard->summaryCounts($user),
        ]);
    }

    /** Per-qualification progress with earned + uncovered course lists. */
    public function qualifications(Request $request): JsonResponse
    {
        return $this->success(
            'Profile qualifications',
            $this->dashboard->qualifications($request->user(), app()->getLocale()),
        );
    }

    /** Completed courses (the "Completed" My-Learnings sub-tab). */
    public function completed(Request $request): JsonResponse
    {
        return $this->success(
            'Profile completed courses',
            $this->dashboard->completedCourses($request->user(), app()->getLocale()),
        );
    }
}
