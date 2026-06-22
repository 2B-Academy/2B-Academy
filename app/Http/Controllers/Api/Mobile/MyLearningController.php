<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Requests\Api\Mobile\SubmitRatingRequest;
use App\Http\Resources\Mobile\CertificateResource;
use App\Http\Resources\Mobile\CourseRatingResource;
use App\Http\Resources\Mobile\LearnerIdentityResource;
use App\Http\Resources\Mobile\MyLearningActiveCourseResource;
use App\Http\Resources\Mobile\MyLearningOverviewResource;
use App\Http\Resources\Mobile\MyLearningSessionResource;
use App\Http\Resources\Mobile\QualificationProgressResource;
use App\Models\Course;
use App\Services\Mobile\MobileCertificateService;
use App\Services\Mobile\MobileRatingService;
use App\Services\Mobile\MyLearningService;
use App\Services\Mobile\QualificationProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mobile S-05 (My Learning) + ratings + qualifications + certificate listings.
 *
 * 📱 MOBILE — Employee/Learner mobile app. Grouped under the single
 * `Mobile` Swagger tag (registered globally in App\OpenApi\Info).
 */
class MyLearningController extends MobileBaseController
{
    public function __construct(
        private readonly MyLearningService            $myLearning,
        private readonly QualificationProgressService $qualifications,
        private readonly MobileCertificateService     $certificates,
        private readonly MobileRatingService          $ratings,
    ) {}

    /**
     * @OA\Get(
     *     path="/mobile/me",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · Identity] Authenticated learner identity",
     *     description="📱 **MOBILE** · Learner identity card · Audience: Employee/Learner mobile app · Returns the authenticated learner's HR-sourced identity (`machine_code`, name, department, job title, image) — used by the mobile app to render the profile header and as the cross-reference key for every audit row.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function me(Request $request): JsonResponse
    {
        // Eager-load jobTitle so the identity block can carry the
        // localized role name without a second round-trip.
        $user = $request->user()->loadMissing('jobTitle');

        return $this->success(
            __('messages.mobile.my_learning_overview'),
            new LearnerIdentityResource($user),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/my-learning/overview",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-05] My Learning overview composite",
     *     description="📱 **MOBILE** · Screen **S-05 — Profile / My Learning** · Audience: Employee/Learner mobile app · Composite payload (counts + previews) for active courses, qualifications, and certificates.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function overview(Request $request): JsonResponse
    {
        $user   = $request->user();
        $locale = app()->getLocale();

        $activePreview         = $this->myLearning->activeCoursesPreview($user);
        $activePaginator       = $this->myLearning->activeCoursesPaginated($user);
        $qualificationsAll     = $this->qualifications->forUser($user, $locale);
        $qualificationsPreview = $this->qualifications->preview(
            $user,
            $locale,
            app(\App\Services\Mobile\MobileSettings::class)->myLearningQualificationsPreviewCount(),
        );
        $certificatesPreview   = $this->certificates->preview($user, $locale);
        $certificatesAll       = $this->certificates->paginate($user, $locale);

        $payload = [
            'counts' => [
                'active_courses' => $activePaginator->total(),
                'qualifications' => $qualificationsAll->count(),
                'certificates'   => $certificatesAll->total(),
            ],
            'previews' => [
                'active_courses' => $activePreview,
                'qualifications' => $qualificationsPreview,
                'certificates'   => $certificatesPreview,
            ],
        ];

        return $this->success(
            __('messages.mobile.my_learning_overview'),
            new MyLearningOverviewResource($payload),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/my-learning/active",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-05] My active courses",
     *     description="📱 **MOBILE** · Screen **S-05 — My Learning · Active Courses tab** · Audience: Employee/Learner mobile app · Paginated active enrolments with live progress / attendance counts and a Live-Now badge.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function active(Request $request): JsonResponse
    {
        $paginator = $this->myLearning->activeCoursesPaginated($request->user());

        return $this->paginated(
            __('messages.mobile.my_learning_courses'),
            MyLearningActiveCourseResource::collection($paginator),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/my-learning/qualifications",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-05] My qualifications progress",
     *     description="📱 **MOBILE** · Screen **S-05 — My Learning · Qualifications tab** · Audience: Employee/Learner mobile app · Derived live from `job_title_qualification_skill` × `course_qualification_skills` × completion sources.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function qualifications(Request $request): JsonResponse
    {
        $rows = $this->qualifications->forUser($request->user(), app()->getLocale());

        return $this->success(
            __('messages.mobile.my_learning_qualifications'),
            QualificationProgressResource::collection($rows),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/my-learning/courses/{course}/sessions",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-05] Course attendance log",
     *     description="📱 **MOBILE** · Screen **S-05 — My Learning · Sessions drilldown** · Audience: Employee/Learner mobile app · Returns one row per cohort session with an `attended` flag derived from the live attendances table.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function sessions(Request $request, int $course): JsonResponse
    {
        $user     = $request->user();
        $courseId = $course;
        $cohortId = (int) \DB::table('users_courses')
            ->where('user_id', $user->id)
            ->where('course_id', $courseId)
            ->value('group_id');

        if ($cohortId <= 0) {
            return $this->error(__('messages.course_not_enrolled'), 403);
        }

        $rows = $this->myLearning->sessionsAttendance($user, $courseId, $cohortId);

        return $this->success(
            __('messages.mobile.my_learning_courses'),
            MyLearningSessionResource::collection($rows),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/my-learning/certificates",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-07] Certificates list",
     *     description="📱 **MOBILE** · Screen **S-07 — Certificates** · Audience: Employee/Learner mobile app · Paginated, derived from final-exam pass + course-evaluation submission (no `user_certificates` table).",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function certificates(Request $request): JsonResponse
    {
        $paginator = $this->certificates->paginate($request->user(), app()->getLocale());

        return $this->paginated(
            __('messages.mobile.my_learning_certificates'),
            CertificateResource::collection($paginator),
        );
    }

    /**
     * @OA\Post(
     *     path="/mobile/my-learning/courses/{course}/rating",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-05] Submit / update cohort rating",
     *     description="📱 **MOBILE** · Screen **S-05 — Rating bottom sheet** · Audience: Employee/Learner mobile app · Settings-driven scale (`rating_min_value` / `rating_max_value`) + conditional comment requirement (`rating_comment_required_at_or_below`).",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"rating"},
     *         @OA\Property(property="rating",  type="integer"),
     *         @OA\Property(property="comment", type="string", nullable=true),
     *     )),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function submitRating(SubmitRatingRequest $request, int $course): JsonResponse
    {
        $courseModel = Course::findOrFail($course);

        $rating = $this->ratings->submit(
            user: $request->user(),
            course: $courseModel,
            rating: (int) $request->validated('rating'),
            comment: $request->validated('comment'),
        );

        return $this->success(
            __('messages.rate_added'),
            new CourseRatingResource((object) [
                'id'         => $rating->id,
                'rating'     => $rating->rating,
                'comment'    => $rating->comment,
                'created_at' => $rating->updated_at ?? $rating->created_at,
            ]),
        );
    }
}
