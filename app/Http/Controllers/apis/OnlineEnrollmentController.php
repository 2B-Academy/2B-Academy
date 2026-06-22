<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\DetachUserRequest;
use App\Http\Requests\Api\EnrollUsersRequest;
use App\Http\Requests\Api\SyncEnrollmentRequest;
use App\Http\Resources\UsersCourseResource;
use App\Models\Course;
use App\Services\OnlineEnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class OnlineEnrollmentController extends ApiController
{
    public function __construct(private readonly OnlineEnrollmentService $enrollmentService) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/online-users",
     *     tags={"Online Enrollment"},
     *     summary="Admin: paginated list of users enrolled in an online course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated online enrollments",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/OnlineEnrollment")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $enrollments = $this->enrollmentService->paginate(
            courseId: $course->id,
            perPage:  (int) $request->get('per_page', 20),
            search:   $request->get('search'),
        );

        return $this->paginated(__('messages.retrieved'), UsersCourseResource::collection($enrollments));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/online-users",
     *     tags={"Online Enrollment"},
     *     summary="Admin: attach users to an online course (additive; does not remove existing enrollments).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_ids"},
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Users attached", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(EnrollUsersRequest $request, Course $course): JsonResponse
    {
        $this->enrollmentService->attach($course, $request->validated('user_ids'));

        return $this->created(__('messages.created'));
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/online-users",
     *     tags={"Online Enrollment"},
     *     summary="Admin: sync users for an online course (replaces the current enrollment list). Supports toggling for_public.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="for_public", type="boolean", description="When true, marks the online course as open to the public."),
     *             @OA\Property(
     *                 property="user_ids",
     *                 type="array",
     *                 @OA\Items(type="integer", example=42)
     *             )
     *         )
     *     ),
     *     @OA\Response(response=200, description="Enrollment list synced", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(SyncEnrollmentRequest $request, Course $course): JsonResponse
    {
        $validated = $request->validated();

        if (array_key_exists('for_public', $validated)) {
            $this->enrollmentService->setPublic($course, (bool) $validated['for_public']);
        }

        if (array_key_exists('user_ids', $validated)) {
            $this->enrollmentService->sync($course, $validated['user_ids']);
        }

        return $this->success(__('messages.updated'));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/online-users",
     *     tags={"Online Enrollment"},
     *     summary="Admin: remove a single user from an online course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"user_id"},
     *             @OA\Property(property="user_id", type="integer", example=42)
     *         )
     *     ),
     *     @OA\Response(response=200, description="User detached", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function destroy(DetachUserRequest $request, Course $course): JsonResponse
    {
        $this->enrollmentService->detach($course, $request->validated('user_id'));

        return $this->deleted();
    }
}
