<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AssignmentReviewRequest;
use App\Http\Requests\Api\AssignmentSubmissionRequest;
use App\Http\Requests\Api\CourseAssignmentRequest;
use App\Http\Resources\CourseAssignmentResource;
use App\Http\Resources\UserCourseAssignmentResource;
use App\Models\Course;
use App\Models\CourseAssignment;
use App\Models\UserCourseAssignment;
use App\Services\CourseAssignmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CourseAssignmentController extends ApiController
{
    public function __construct(private readonly CourseAssignmentService $service) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/assignments",
     *     tags={"Course Assignments"},
     *     summary="List assignments for a course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course assignments",
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
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Course $course): JsonResponse
    {
        return $this->success(__('messages.retrieved'),
            CourseAssignmentResource::collection($this->service->listForCourse($course))
        );
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/assignments",
     *     tags={"Course Assignments"},
     *     summary="Create an assignment for a course (admin only). Uses multipart/form-data.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","file"},
     *                 @OA\Property(property="title", type="string", maxLength=255),
     *                 @OA\Property(property="file",  type="string", format="binary", description="Assignment instructions file (max 20 MB).")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseAssignment"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function indexAll(Request $request): JsonResponse
    {
        $assignments = $this->service->paginateAll(
            $request->integer('course_id') ?: null,
            $request->get('search'),
            $request->integer('per_page', 20),
        );
        return $this->paginated(__('messages.retrieved'), CourseAssignmentResource::collection($assignments));
    }

    public function allSubmissions(Request $request): JsonResponse
    {
        $submissions = $this->service->paginateAllSubmissions(
            $request->integer('user_id') ?: null,
            $request->integer('course_id') ?: null,
            $request->get('status'),
            $request->integer('per_page', 20),
        );
        return $this->paginated(__('messages.retrieved'), UserCourseAssignmentResource::collection($submissions));
    }

    public function store(Course $course, CourseAssignmentRequest $request): JsonResponse
    {
        $v = $request->validated();
        $assignment = $this->service->create($course, $v['title'], $request->file('file'), $v['due_date'] ?? null);
        return $this->created(__('messages.created'), new CourseAssignmentResource($assignment));
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/assignments/{assignment}",
     *     tags={"Course Assignments"},
     *     summary="Update an assignment (admin only). Uses multipart/form-data if uploading a new file.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title"},
     *                 @OA\Property(property="title", type="string", maxLength=255),
     *                 @OA\Property(property="file",  type="string", format="binary", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseAssignment"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Course $course, CourseAssignment $assignment, CourseAssignmentRequest $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $v = $request->validated();
        $assignment = $this->service->update($assignment, $v['title'], $request->file('file'), $v['due_date'] ?? null);
        return $this->success(__('messages.updated'), new CourseAssignmentResource($assignment));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/assignments/{assignment}",
     *     tags={"Course Assignments"},
     *     summary="Delete an assignment (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, CourseAssignment $assignment): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $this->service->delete($assignment);
        return $this->deleted(__('messages.deleted'));
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/assignments/{assignment}/submissions",
     *     tags={"Course Assignments"},
     *     summary="List user submissions for an assignment (admin only, paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated submissions",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseAssignmentSubmission")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function submissions(Course $course, CourseAssignment $assignment, Request $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);
        $submissions = $this->service->listSubmissions($assignment, (int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $submissions);
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/assignments/{assignment}/submissions/{submission}/review",
     *     tags={"Course Assignments"},
     *     summary="Review a user submission — add feedback and/or score (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="submission", in="path", required=true,
     *         description="Submission id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="feedback", type="string", maxLength=2000, nullable=true),
     *             @OA\Property(property="score",    type="string", maxLength=50,   nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Reviewed",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseAssignmentSubmission"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function review(
        Course $course,
        CourseAssignment $assignment,
        UserCourseAssignment $submission,
        AssignmentReviewRequest $request
    ): JsonResponse {
        abort_if($assignment->course_id !== $course->id, 404);
        abort_if($submission->course_assignment_id !== $assignment->id, 404);

        $data       = $request->validated();
        $submission = $this->service->reviewSubmission($submission, $data['feedback'] ?? null, $data['score'] ?? null);

        return $this->success(__('messages.updated'), new UserCourseAssignmentResource($submission));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/assignments/{assignment}/submit",
     *     tags={"Course Assignments"},
     *     summary="Submit (or replace) the authenticated user's file for an assignment.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary", description="Submission file (max 20 MB).")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Submitted",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseAssignmentSubmission"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function submit(Course $course, CourseAssignment $assignment, AssignmentSubmissionRequest $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);

        /** @var \App\Models\User $user */
        $user       = $request->user();
        $submission = $this->service->submitFile($assignment, $user, $request->file('file'));

        return $this->success(__('messages.updated'), new UserCourseAssignmentResource($submission));
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/assignments/{assignment}/my-submission",
     *     tags={"Course Assignments"},
     *     summary="Get the authenticated user's own submission for an assignment (null if none).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="assignment", in="path", required=true,
     *         description="Assignment id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User submission (may be null)",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     nullable=true,
     *                     ref="#/components/schemas/CourseAssignmentSubmission"
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function mySubmission(Course $course, CourseAssignment $assignment, Request $request): JsonResponse
    {
        abort_if($assignment->course_id !== $course->id, 404);

        /** @var \App\Models\User $user */
        $user       = $request->user();
        $submission = $this->service->findSubmission($assignment->id, $user->id);

        return $this->success(__('messages.retrieved'),
            $submission ? new UserCourseAssignmentResource($submission) : null
        );
    }
}
