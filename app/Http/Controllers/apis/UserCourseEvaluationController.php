<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\SubmitCourseEvaluationRequest;
use App\Http\Resources\EvaluationCategoryResource;
use App\Models\Course;
use App\Services\UserCourseEvaluationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class UserCourseEvaluationController extends ApiController
{
    public function __construct(private readonly UserCourseEvaluationService $evalService) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/evaluate",
     *     tags={"Course Evaluations"},
     *     summary="Get the course evaluation form (categories + questions) and whether the current user has already submitted it.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Course evaluation form",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(
     *                     @OA\Property(
     *                         property="result",
     *                         type="object",
     *                         @OA\Property(property="already_evaluated", type="boolean"),
     *                         @OA\Property(
     *                             property="evaluation_categories",
     *                             type="array",
     *                             @OA\Items(ref="#/components/schemas/EvaluationCategory")
     *                         )
     *                     )
     *                 )
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound", description="Course not found or not evaluatable")
     * )
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        abort_if(!$course->is_evaluate, 404);

        return $this->success(__('messages.retrieved'), [
            'already_evaluated'    => $this->evalService->hasEvaluated($request->user()->id, $course->id),
            'evaluation_categories' => EvaluationCategoryResource::collection($this->evalService->getForm()),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/evaluate",
     *     tags={"Course Evaluations"},
     *     summary="Submit a course evaluation (once per user/course).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="course", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"instructor_id","questions"},
     *             @OA\Property(property="instructor_id", type="integer", description="Existing instructor id."),
     *             @OA\Property(
     *                 property="questions",
     *                 type="object",
     *                 description="Map of evaluation_question_id => answer (string for text, integer for stars/ratings).",
     *                 example={"1": 5, "2": "Great course!"}
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Submitted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden", description="Course is not evaluatable"),
     *     @OA\Response(response=409, description="Already evaluated", @OA\JsonContent(ref="#/components/schemas/ErrorResponse")),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(SubmitCourseEvaluationRequest $request, Course $course): JsonResponse
    {
        abort_if(!$course->is_evaluate, 403);

        if ($this->evalService->hasEvaluated($request->user()->id, $course->id)) {
            return $this->error(__('messages.already_evaluated'), 409);
        }

        $validated = $request->validated();

        $this->evalService->submit(
            $request->user(),
            $course,
            $validated['instructor_id'],
            $validated['questions'],
        );

        return $this->created(__('messages.created'));
    }
}
