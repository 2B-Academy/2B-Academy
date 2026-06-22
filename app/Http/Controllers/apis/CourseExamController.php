<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseExamRequest;
use App\Http\Resources\CourseExamResource;
use App\Models\Course;
use App\Models\CourseExam;
use App\Services\CourseExamService;
use Illuminate\Http\JsonResponse;
use OpenApi\Annotations as OA;

class CourseExamController extends ApiController
{
    public function __construct(private readonly CourseExamService $service) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/exams",
     *     tags={"Course Exams"},
     *     summary="List exams for a course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course exams",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseExam")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Course $course): JsonResponse
    {
        $exams = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseExamResource::collection($exams));
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/exams/{exam}",
     *     tags={"Course Exams"},
     *     summary="Show an exam with its questions and answers.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="exam", in="path", required=true,
     *         description="Exam id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course exam",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseExam"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Course $course, CourseExam $exam): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $exam = $this->service->find($exam->id);
        return $this->success(__('messages.retrieved'), new CourseExamResource($exam));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/exams",
     *     tags={"Course Exams"},
     *     summary="Create an exam with questions and answers (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_id","title","degree","questions"},
     *             @OA\Property(property="section_id", type="integer"),
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="degree",     type="integer", minimum=1),
     *             @OA\Property(property="is_final",   type="boolean", nullable=true),
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     required={"question","answers"},
     *                     @OA\Property(property="question", ref="#/components/schemas/TranslatedString"),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             required={"answer","is_correct"},
     *                             @OA\Property(property="answer",     ref="#/components/schemas/TranslatedString"),
     *                             @OA\Property(property="is_correct", type="boolean")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseExam"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Course $course, CourseExamRequest $request): JsonResponse
    {
        $exam = $this->service->create($course, $request->validated());
        return $this->created(__('messages.created'), new CourseExamResource($exam));
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/exams/{exam}",
     *     tags={"Course Exams"},
     *     summary="Update an exam (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="exam", in="path", required=true,
     *         description="Exam id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_id","title","degree","questions"},
     *             @OA\Property(property="section_id", type="integer"),
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="degree",     type="integer", minimum=1),
     *             @OA\Property(property="is_final",   type="boolean", nullable=true),
     *             @OA\Property(
     *                 property="questions",
     *                 type="array",
     *                 @OA\Items(
     *                     type="object",
     *                     @OA\Property(property="question", ref="#/components/schemas/TranslatedString"),
     *                     @OA\Property(
     *                         property="answers",
     *                         type="array",
     *                         @OA\Items(
     *                             type="object",
     *                             @OA\Property(property="answer",     ref="#/components/schemas/TranslatedString"),
     *                             @OA\Property(property="is_correct", type="boolean")
     *                         )
     *                     )
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseExam"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Course $course, CourseExam $exam, CourseExamRequest $request): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $updated = $this->service->update($exam, $request->validated());
        return $this->success(__('messages.updated'), new CourseExamResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/exams/{exam}",
     *     tags={"Course Exams"},
     *     summary="Delete an exam (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="exam", in="path", required=true,
     *         description="Exam id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, CourseExam $exam): JsonResponse
    {
        abort_if($exam->course_id !== $course->id, 404);
        $this->service->delete($exam);
        return $this->deleted(__('messages.deleted'));
    }
}
