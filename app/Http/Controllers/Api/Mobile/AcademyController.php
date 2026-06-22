<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\Mobile;

use App\Http\Requests\Api\Mobile\EnrolmentRequest;
use App\Http\Resources\Mobile\AcademyCourseCardResource;
use App\Http\Resources\Mobile\AcademyCourseDetailResource;
use App\Http\Resources\Mobile\AcademyEntrySummaryResource;
use App\Http\Resources\Mobile\AcademyScopeChipResource;
use App\Http\Resources\Mobile\EnrolmentConfirmationResource;
use App\Models\Course;
use App\Services\Mobile\AcademyService;
use App\Services\Mobile\EnrolmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mobile S-01 → S-04: Academy entry, list, detail, and enrolment.
 *
 * 📱 MOBILE — Employee/Learner mobile app. Every operation in this
 * controller is grouped under the single `Mobile` Swagger tag,
 * registered globally in App\OpenApi\Info. The S-XX screen identifier
 * is preserved in the operation summary so the per-screen grouping
 * is still readable inside the collapsed `Mobile` sidebar group.
 *
 * All controller logic is razor-thin: extract input, ask a service,
 * wrap the answer in a resource, return. No business logic and no
 * direct DB access.
 */
class AcademyController extends MobileBaseController
{
    public function __construct(
        private readonly AcademyService   $academy,
        private readonly EnrolmentService $enrolment,
    ) {}

    /**
     * @OA\Get(
     *     path="/mobile/academy/summary",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-01] Academy entry card data",
     *     description="📱 **MOBILE** · Screen **S-01 — Academy entry card** · Audience: Employee/Learner mobile app · Returns the available-courses count + has-available flag for the Academy card.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function summary(Request $request): JsonResponse
    {
        $summary = $this->academy->summaryFor($request->user());

        return $this->success(
            __('messages.mobile.academy_summary'),
            new AcademyEntrySummaryResource($summary),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/academy/scopes",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-02] Scope chips (All / Special / General)",
     *     description="📱 **MOBILE** · Screen **S-02 — Course list** · Audience: Employee/Learner mobile app · Returns the three fixed filter chips with per-scope availability counts. **Special** = courses tied to THIS employee's job-title qualifications. **General** = courses open to anyone (`for_public`).",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function scopes(Request $request): JsonResponse
    {
        $chips = $this->academy->scopeChipsFor($request->user(), app()->getLocale());

        return $this->success(
            __('messages.mobile.academy_scopes'),
            AcademyScopeChipResource::collection($chips),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/academy/courses",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-02] Available courses list",
     *     description="📱 **MOBILE** · Screen **S-02 — Course list** · Audience: Employee/Learner mobile app · Paginated feed of courses with at least one joinable upcoming cohort for THIS user.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="category_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="search",      in="query", @OA\Schema(type="string")),
     *     @OA\Parameter(name="per_page",    in="query", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="scope",       in="query", description="Filter chip: all | special | general", @OA\Schema(type="string", enum={"all","special","general"})),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function courses(Request $request): JsonResponse
    {
        $paginator = $this->academy->listAvailable(
            user: $request->user(),
            categoryId: $request->integer('category_id') ?: null,
            search: $request->string('search')->toString() ?: null,
            perPage: $request->integer('per_page') ?: null,
            scope: $request->string('scope')->toString() ?: null,
        );

        return $this->paginated(
            __('messages.mobile.academy_courses'),
            AcademyCourseCardResource::collection($paginator),
        );
    }

    /**
     * @OA\Get(
     *     path="/mobile/academy/courses/{course}",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-03] Course detail",
     *     description="📱 **MOBILE** · Screen **S-03 — Course detail & enrolment** · Audience: Employee/Learner mobile app · Returns the full detail view-model including anchor cohort + typed CTA state.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="OK")
     * )
     */
    public function show(Request $request, int $course): JsonResponse
    {
        $courseModel  = $this->academy->findDetail($course);
        $anchorCohort = $this->academy->anchorCohortFor($courseModel, $request->user());
        $ctaState     = $this->academy->resolveCtaState($courseModel, $request->user(), $anchorCohort);

        $resource = new AcademyCourseDetailResource($courseModel);
        $resource->additional = [
            'anchor_cohort' => $anchorCohort,
            'cta_state'     => $ctaState,
        ];

        return $this->success(
            __('messages.mobile.academy_course_detail'),
            $resource,
        );
    }

    /**
     * @OA\Post(
     *     path="/mobile/academy/courses/{course}/enrol",
     *     tags={"Mobile"},
     *     summary="📱 [MOBILE · S-03→S-04] Enrol into a cohort",
     *     description="📱 **MOBILE** · Screens **S-03 → S-04 — Enrolment confirmation** · Audience: Employee/Learner mobile app · First-come-first-served enrolment race; row-locks the cohort + checks capacity + deadline inside a transaction.",
     *     @OA\Parameter(ref="#/components/parameters/MobileAuthorization"),
     *     @OA\Parameter(ref="#/components/parameters/EmployeeCode"),
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=false, @OA\JsonContent(@OA\Property(property="cohort_id", type="integer"))),
     *     @OA\Response(response=201, description="Enrolled"),
     *     @OA\Response(response=409, description="Cohort full / closed / no cohort")
     * )
     */
    public function enrol(EnrolmentRequest $request, int $course): JsonResponse
    {
        $courseModel = Course::findOrFail($course);

        $result = $this->enrolment->enrol(
            user: $request->user(),
            course: $courseModel,
            requestedCohortId: $request->validated('cohort_id'),
        );

        $resource = new EnrolmentConfirmationResource([
            'outcome' => $result['outcome'],
            'cohort'  => $result['cohort'],
            'course'  => $courseModel,
        ]);

        $payload = [
            'status'  => $result['outcome']->isSuccess() ? 'success' : 'error',
            'message' => __($result['outcome']->messageKey()),
            'result'  => $resource,
        ];

        return response()->json($payload, $result['outcome']->httpStatus());
    }
}
