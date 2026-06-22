<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\LectureProgressRequest;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Services\LectureProgressService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class LectureProgressController extends ApiController
{
    public function __construct(private readonly LectureProgressService $progressService) {}

    /**
     * @OA\Post(
     *     path="/courses/{course}/lectures/{lecture}/progress",
     *     tags={"Lecture Progress"},
     *     summary="Report the authenticated user's watch progress for a lecture. Auto-marks as completed at 90%+.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course",
     *         in="path",
     *         required=true,
     *         description="Course identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="lecture",
     *         in="path",
     *         required=true,
     *         description="Lecture identifier",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"progress"},
     *             @OA\Property(property="progress", type="integer", minimum=0, maximum=100, example=85, description="Watch percentage 0-100.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Progress updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="lecture_id", type="integer"),
     *                     @OA\Property(property="progress",   type="integer", example=85),
     *                     @OA\Property(property="completed",  type="boolean")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(LectureProgressRequest $request, Course $course, CourseLecture $lecture): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);

        $progress = $this->progressService->track(
            $request->user()->id,
            $lecture->id,
            $request->validated('progress'),
        );

        return $this->success(__('messages.updated'), [
            'lecture_id' => $progress->lecture_id,
            'progress'   => $progress->progress,
            'completed'  => (bool) $progress->completed,
        ]);
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/my-progress",
     *     tags={"Lecture Progress"},
     *     summary="Get the authenticated user's overall course completion % and per-lecture breakdown.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course",
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
     *                     @OA\Property(property="course_id",        type="integer"),
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
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Request $request, Course $course): JsonResponse
    {
        $userId = $request->user()->id;

        return $this->success(__('messages.retrieved'), [
            'course_id'       => $course->id,
            'overall_progress' => $this->progressService->getCourseProgress($userId, $course->id),
            'lectures'         => $this->progressService->getLectureProgress($userId, $course->id),
        ]);
    }
}
