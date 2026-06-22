<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseSessionRequest;
use App\Http\Resources\CourseSessionResource;
use App\Models\Course;
use App\Models\CourseSession;
use App\Services\CourseSessionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CourseSessionController extends ApiController
{
    public function __construct(private readonly CourseSessionService $service) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/sessions",
     *     tags={"Course Sessions"},
     *     summary="List offline course sessions (paginated, admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="section_id", in="query", required=false,
     *         description="Filter by section id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated sessions",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseSession")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Course $course, Request $request): JsonResponse
    {
        $sessions = $this->service->paginate(
            $course,
            (int) $request->get('per_page', 20),
            $request->get('section_id') ? (int) $request->get('section_id') : null
        );
        return $this->paginated(__('messages.retrieved'), $sessions);
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/sessions",
     *     tags={"Course Sessions"},
     *     summary="Create an offline session for a course (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_id","title"},
     *             @OA\Property(property="section_id",   type="integer"),
     *             @OA\Property(property="title",        type="string", maxLength=255),
     *             @OA\Property(property="session_date", type="string", format="date",  nullable=true),
     *             @OA\Property(property="time_from",    type="string", example="09:00", nullable=true, description="HH:mm"),
     *             @OA\Property(property="time_to",      type="string", example="11:00", nullable=true, description="HH:mm"),
     *             @OA\Property(property="location",     type="string", maxLength=255, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseSession"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Course $course, CourseSessionRequest $request): JsonResponse
    {
        $session = $this->service->create($course, $request->validated());
        return $this->created(__('messages.created'), new CourseSessionResource($session));
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/sessions/{session}",
     *     tags={"Course Sessions"},
     *     summary="Update an offline session (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="session", in="path", required=true,
     *         description="Session id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_id","title"},
     *             @OA\Property(property="section_id",   type="integer"),
     *             @OA\Property(property="title",        type="string", maxLength=255),
     *             @OA\Property(property="session_date", type="string", format="date",  nullable=true),
     *             @OA\Property(property="time_from",    type="string", example="09:00", nullable=true),
     *             @OA\Property(property="time_to",      type="string", example="11:00", nullable=true),
     *             @OA\Property(property="location",     type="string", maxLength=255, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseSession"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Course $course, CourseSession $session, CourseSessionRequest $request): JsonResponse
    {
        abort_if($session->course_id !== $course->id, 404);
        $session = $this->service->update($session, $request->validated());
        return $this->success(__('messages.updated'), new CourseSessionResource($session));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/sessions/{session}",
     *     tags={"Course Sessions"},
     *     summary="Delete an offline session (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="session", in="path", required=true,
     *         description="Session id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, CourseSession $session): JsonResponse
    {
        abort_if($session->course_id !== $course->id, 404);
        $this->service->delete($session);
        return $this->deleted(__('messages.deleted'));
    }
}
