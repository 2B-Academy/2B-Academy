<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\SubmitCourseRatingRequest;
use App\Http\Resources\CourseRatingResource;
use App\Models\Course;
use App\Models\CourseRating;
use App\Services\CourseRatingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CourseRatingController extends ApiController
{
    public function __construct(private readonly CourseRatingService $ratingService) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/ratings",
     *     tags={"Course Ratings"},
     *     summary="List ratings for a course (admin only, paginated).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="user_id", in="query", required=false,
     *         description="Filter by user id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated ratings",
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
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function index(Request $request, Course $course): JsonResponse
    {
        $ratings = $this->ratingService->paginateForCourse(
            courseId: $course->id,
            perPage:  (int) $request->get('per_page', 20),
            userId:   $request->get('user_id'),
        );

        return $this->paginated(__('messages.retrieved'), CourseRatingResource::collection($ratings));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/ratings",
     *     tags={"Course Ratings"},
     *     summary="Submit or update the authenticated user's rating for a course.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"rating"},
     *             @OA\Property(property="rating", type="integer", minimum=1, maximum=5),
     *             @OA\Property(property="review", type="string", maxLength=1000, nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rating saved",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseRating"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(SubmitCourseRatingRequest $request, Course $course): JsonResponse
    {
        $rating = $this->ratingService->submitRating($course, $request->user()->id, $request->validated());

        return $this->success(__('messages.created'), new CourseRatingResource($rating->load('user')));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/ratings/{rating}",
     *     tags={"Course Ratings"},
     *     summary="Delete a course rating (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Parameter(
     *         name="rating", in="path", required=true,
     *         description="Rating id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, CourseRating $rating): JsonResponse
    {
        abort_if($rating->course_id !== $course->id, 404);

        $this->ratingService->delete($rating);

        return $this->deleted();
    }

    /**
     * All ratings across all courses (admin only).
     * Supports filters: ?course_id, ?instructor_id, ?search
     */
    public function allRatings(Request $request): JsonResponse
    {
        $ratings = $this->ratingService->paginateAll(
            perPage:      (int) $request->get('per_page', 20),
            courseId:     $request->get('course_id') ? (int) $request->get('course_id') : null,
            instructorId: $request->get('instructor_id') ? (int) $request->get('instructor_id') : null,
            search:       $request->get('search'),
        );

        return $this->paginated(__('messages.retrieved'), CourseRatingResource::collection($ratings));
    }
}
