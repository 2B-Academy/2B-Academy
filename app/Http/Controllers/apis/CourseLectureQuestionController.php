<?php

namespace App\Http\Controllers\apis;

use App\Http\Resources\CourseLectureQuestionResource;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseLectureQuestion;
use App\Services\CourseLectureQuestionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CourseLectureQuestionController extends ApiController
{
    public function __construct(private readonly CourseLectureQuestionService $questionService) {}

    /**
     * @OA\Get(
     *     path="/lecture-questions",
     *     tags={"Lecture Questions"},
     *     summary="List lecture questions with optional filters (admin only, paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course_id", in="query", required=false,
     *         description="Filter by course id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="lecture_id", in="query", required=false,
     *         description="Filter by lecture id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="user_id", in="query", required=false,
     *         description="Filter by user id (asker).",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="answered", in="query", required=false,
     *         description="Filter by answered/unanswered state.",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated lecture questions",
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
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden")
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $filters = array_filter([
            'course_id'  => $request->get('course_id'),
            'lecture_id' => $request->get('lecture_id'),
            'user_id'    => $request->get('user_id'),
            'answered'   => $request->has('answered') ? filter_var($request->get('answered'), FILTER_VALIDATE_BOOLEAN) : null,
        ], fn ($v) => $v !== null);

        $questions = $this->questionService->paginate(
            (int) $request->get('per_page', 20),
            $filters,
        );

        return $this->paginated(__('messages.retrieved'), CourseLectureQuestionResource::collection($questions));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/lectures/{lecture}/questions",
     *     tags={"Lecture Questions"},
     *     summary="Submit a question on a lecture (authenticated user).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="lecture", in="path", required=true,
     *         description="Lecture id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"question"},
     *             @OA\Property(property="question", type="string", maxLength=2000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Question submitted",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/LectureQuestion"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Request $request, Course $course, CourseLecture $lecture): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);

        $data = $request->validate([
            'question' => 'required|string|max:2000',
        ]);

        $question = $this->questionService->submit([
            'question'   => $data['question'],
            'course_id'  => $course->id,
            'lecture_id' => $lecture->id,
            'user_id'    => $request->user()->id,
        ]);

        return $this->created(__('messages.created'), new CourseLectureQuestionResource($question));
    }

    /**
     * @OA\Put(
     *     path="/lecture-questions/{question}/answer",
     *     tags={"Lecture Questions"},
     *     summary="Post an answer to a lecture question (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="question", in="path", required=true,
     *         description="Lecture question id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"answer"},
     *             @OA\Property(property="answer", type="string", maxLength=5000)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Answered",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/LectureQuestion"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function answer(Request $request, CourseLectureQuestion $question): JsonResponse
    {
        $data = $request->validate([
            'answer' => 'required|string|max:5000',
        ]);

        $question = $this->questionService->answer($question, $request->user()->id, $data['answer']);

        return $this->success(__('messages.updated'), new CourseLectureQuestionResource($question->load(['user', 'lecture', 'answeredBy'])));
    }

    /**
     * @OA\Delete(
     *     path="/lecture-questions/{question}",
     *     tags={"Lecture Questions"},
     *     summary="Delete a lecture question (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="question", in="path", required=true,
     *         description="Lecture question id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(CourseLectureQuestion $question): JsonResponse
    {
        $this->questionService->delete($question);
        return $this->deleted();
    }
}
