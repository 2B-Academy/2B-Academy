<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\CourseResource;
use App\Http\Resources\UserExamResource;
use App\Models\User;
use App\Services\LectureProgressService;
use App\Services\UserDashboardService;
use App\Services\UserExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserDashboardController extends ApiController
{
    public function __construct(
        private readonly UserDashboardService  $dashboardService,
        private readonly UserExamService       $examService,
        private readonly LectureProgressService $progressService,
    ) {}

    /**
     * @OA\Get(
     *     path="/my/dashboard",
     *     tags={"My"},
     *     summary="Get the authenticated user's personal stats summary.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Personal dashboard stats",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="enrolled_courses",  type="integer", example=5),
     *                     @OA\Property(property="completed_courses", type="integer", example=2),
     *                     @OA\Property(property="certificates",      type="integer", example=2),
     *                     @OA\Property(property="exams_taken",       type="integer", example=8),
     *                     @OA\Property(property="exams_passed",      type="integer", example=6)
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function dashboard(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success(
            __('messages.retrieved'),
            $this->dashboardService->getStats($user),
        );
    }

    /**
     * @OA\Get(
     *     path="/my/courses",
     *     tags={"My"},
     *     summary="List the authenticated user's enrolled + public courses.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Courses available to the user",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Course")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myCourses(Request $request): JsonResponse
    {
        /** @var User $user */
        $user    = $request->user();
        $courses = $this->dashboardService->getMyCourses($user);

        return $this->success(__('messages.retrieved'), CourseResource::collection($courses));
    }

    /**
     * @OA\Get(
     *     path="/my/exams",
     *     tags={"My"},
     *     summary="List the authenticated user's own exam history.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="User exam history",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/UserExam")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myExams(Request $request): JsonResponse
    {
        $exams = $this->examService->getUserExams($request->user()->id);

        return $this->success(__('messages.retrieved'), UserExamResource::collection($exams));
    }

    /**
     * @OA\Get(
     *     path="/my/exams/{id}",
     *     tags={"My"},
     *     summary="Get one of the authenticated user's exam results with the full answer breakdown.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/IdPath"),
     *     @OA\Response(
     *         response=200,
     *         description="Exam result with answers",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/UserExam"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function myExam(Request $request, int $id): JsonResponse
    {
        $exam = $this->examService->getUserExam($request->user()->id, $id);

        if (!$exam) {
            return $this->notFound();
        }

        return $this->success(__('messages.retrieved'), new UserExamResource($exam));
    }

    /**
     * @OA\Get(
     *     path="/my/assignments",
     *     tags={"My"},
     *     summary="List assignments across the user's enrolled courses, including submission status.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Assignments with submission status",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseAssignment")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myAssignments(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success(
            __('messages.retrieved'),
            $this->dashboardService->getMyAssignments($user),
        );
    }

    /**
     * @OA\Get(
     *     path="/my/certificates",
     *     tags={"My"},
     *     summary="List the authenticated user's earned certificates.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Earned certificates",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="type",      type="string", example="course"),
     *                         @OA\Property(property="course_id", type="integer", nullable=true),
     *                         @OA\Property(property="course",    type="string",  nullable=true, description="Localized course title."),
     *                         @OA\Property(property="earned_at", type="string",  format="date", nullable=true)
     *                     )
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myCertificates(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        $locale = app()->getLocale();
        $certificates = $this->dashboardService->getCertificates($user)->map(fn ($cert) => [
            'type'       => $cert['type'],
            'course_id'  => $cert['data']->course?->id,
            'course'     => $cert['data']->course?->getTranslation('title', $locale),
            'earned_at'  => $cert['data']->created_at?->toDateString(),
        ]);

        return $this->success(__('messages.retrieved'), $certificates);
    }

    /**
     * @OA\Get(
     *     path="/my/ratings",
     *     tags={"My"},
     *     summary="List the authenticated user's own course ratings.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="User course ratings",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseRating")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myRatings(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success(
            __('messages.retrieved'),
            $this->dashboardService->getMyRatings($user->id),
        );
    }

    /**
     * @OA\Get(
     *     path="/my/lecture-questions",
     *     tags={"My"},
     *     summary="List the authenticated user's own lecture questions with their answers.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="User lecture questions",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/LectureQuestion")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myLectureQuestions(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user();

        return $this->success(
            __('messages.retrieved'),
            $this->dashboardService->getMyLectureQuestions($user->id),
        );
    }

    /**
     * @OA\Get(
     *     path="/my/progress/{courseId}",
     *     tags={"My"},
     *     summary="Get course completion percentage with per-lecture progress detail for the authenticated user.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="courseId",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course progress detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="course_id",        type="integer", example=12),
     *                     @OA\Property(property="overall_progress", type="integer", example=75, description="Percentage 0-100."),
     *                     @OA\Property(
     *                         property="lectures",
     *                         type="array",
     *                         @OA\Items(ref="#/components/schemas/LectureProgress")
     *                     )
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function myProgress(Request $request, int $courseId): JsonResponse
    {
        $userId = $request->user()->id;

        return $this->success(__('messages.retrieved'), [
            'course_id'        => $courseId,
            'overall_progress' => $this->progressService->getCourseProgress($userId, $courseId),
            'lectures'         => $this->progressService->getLectureProgress($userId, $courseId),
        ]);
    }
}
