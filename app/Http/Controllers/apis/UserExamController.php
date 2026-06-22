<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\SubmitExamRequest;
use App\Http\Resources\UserExamResource;
use App\Models\Course;
use App\Models\CourseExam;
use App\Services\UserExamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserExamController extends ApiController
{
    public function __construct(private readonly UserExamService $examService) {}

    /**
     * @OA\Post(
     *     path="/courses/{course}/exams/{exam}/submit",
     *     tags={"Exams"},
     *     summary="Submit exam answers for the authenticated user — auto-graded, returns the result.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="exam",
     *         in="path",
     *         required=true,
     *         description="Exam identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"questions"},
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 minItems=1,
     *                 @OA\Items(
     *                     type="object",
     *                     required={"question_id","question_title","answer_id"},
     *                     @OA\Property(property="question_id",    type="integer", example=42),
     *                     @OA\Property(property="question_title", type="string",  example="What is 2 + 2?"),
     *                     @OA\Property(property="answer_id",      type="integer", example=7)
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Exam submitted and graded",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/UserExam"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(
     *         response=409,
     *         description="Exam already submitted by this user",
     *         @OA\JsonContent(ref="#/components/schemas/ErrorResponse")
     *     ),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function submit(SubmitExamRequest $request, Course $course, CourseExam $exam): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);

        if ($this->examService->hasAlreadySubmitted($request->user()->id, $exam->id)) {
            return $this->error(__('messages.exam_already_submitted'), 409);
        }

        $userExam = $this->examService->submit(
            $request->user(),
            $course,
            $exam,
            $request->validated('questions'),
        );

        return $this->created(__('messages.created'), new UserExamResource($userExam));
    }

    /** User: list own exam history. */
    public function index(Request $request): JsonResponse
    {
        $exams = $this->examService->getUserExams($request->user()->id);

        return $this->success(__('messages.retrieved'), UserExamResource::collection($exams));
    }

    /** User: get own exam result with answers. */
    public function show(Request $request, int $id): JsonResponse
    {
        $exam = $this->examService->getUserExam($request->user()->id, $id);

        if (!$exam) {
            return $this->notFound();
        }

        return $this->success(__('messages.retrieved'), new UserExamResource($exam));
    }
}
