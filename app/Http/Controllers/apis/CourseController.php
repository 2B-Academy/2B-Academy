<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseRequest;
use App\Http\Resources\CourseDetailResource;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\CourseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CourseController extends ApiController
{
    public function __construct(private readonly CourseService $courseService) {}

    /**
     * @OA\Get(
     *     path="/courses",
     *     tags={"Courses"},
     *     summary="List courses (paginated, with filters).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Parameter(
     *         name="category_id", in="query", required=false,
     *         description="Filter by category id.",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="active", in="query", required=false,
     *         description="Filter by active flag.",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="course_type", in="query", required=false,
     *         description="Filter by course type (online or offline).",
     *         @OA\Schema(type="string", enum={"online","offline"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated courses",
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
    public function index(Request $request): JsonResponse
    {
        $active = null;
        if ($request->has('active')) {
            $active = filter_var($request->active, FILTER_VALIDATE_BOOLEAN);
        }

        $status = $request->filled('status') && $request->get('status') !== 'all'
            ? (string) $request->get('status')
            : null;

        $courses = $this->courseService->list(
            perPage:    (int) $request->get('per_page', 15),
            search:     $request->get('search'),
            categoryId: $request->integer('category_id') ?: null,
            active:     $active,
            courseType: $request->get('course_type'),
            status:     $status,
        );

        return $this->paginated(__('messages.retrieved'), CourseResource::collection($courses));
    }

    /**
     * @OA\Get(
     *     path="/courses/tab-counts",
     *     tags={"Courses"},
     *     summary="Return total course counts grouped by admin tab. Computed in a single aggregate query.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Tab counts",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="all",      type="integer"),
     *                     @OA\Property(property="active",   type="integer"),
     *                     @OA\Property(property="inactive", type="integer"),
     *                     @OA\Property(property="pending",  type="integer"),
     *                     @OA\Property(property="upcoming", type="integer"),
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized")
     * )
     */
    public function tabCounts(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            $this->courseService->tabCounts(),
        );
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}",
     *     tags={"Courses"},
     *     summary="Show a course (with sections, lectures, exams, ratings).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Course detail",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseDetail"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Course $course): JsonResponse
    {
        $course = $this->courseService->findOrFail($course->id);

        return $this->success(
            __('messages.retrieved'),
            new CourseDetailResource($course),
        );
    }

    /**
     * @OA\Post(
     *     path="/courses",
     *     tags={"Courses"},
     *     summary="Create a course (admin only). Uses multipart/form-data for the image upload.",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","description","category_id","hours","certificate","image","instructors"},
     *                 @OA\Property(property="course_type",           type="string", enum={"online","offline"}),
     *                 @OA\Property(property="title",                 type="string", maxLength=255),
     *                 @OA\Property(property="title_for_certificate", type="string", maxLength=255, nullable=true),
     *                 @OA\Property(property="description",           type="string"),
     *                 @OA\Property(property="category_id",           type="integer"),
     *                 @OA\Property(property="intro_video",           type="string", nullable=true),
     *                 @OA\Property(property="price",                 type="number", format="float", nullable=true),
     *                 @OA\Property(property="currency",              type="string", maxLength=10, nullable=true),
     *                 @OA\Property(property="hours",                 type="integer", minimum=1),
     *                 @OA\Property(property="language",              type="string", nullable=true),
     *                 @OA\Property(property="level",                 type="string", nullable=true),
     *                 @OA\Property(property="certificate",           type="boolean"),
     *                 @OA\Property(property="image",                 type="string", format="binary"),
     *                 @OA\Property(property="active",                type="boolean", nullable=true),
     *                 @OA\Property(property="outside_materials",     type="boolean", nullable=true),
     *                 @OA\Property(property="is_evaluate",           type="boolean", nullable=true),
     *                 @OA\Property(property="allow_attendances",     type="boolean", nullable=true),
     *                 @OA\Property(property="instructors",           type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="qualification_skill_ids", type="array", @OA\Items(type="integer"), nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Course"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(CourseRequest $request): JsonResponse
    {
        $course = $this->courseService->create(
            $request->validated(),
            $request->file('image'),
        );

        return $this->created(
            __('messages.created'),
            new CourseResource($course),
        );
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}",
     *     tags={"Courses"},
     *     summary="Update a course (admin only). Uses multipart/form-data when uploading a new image.",
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
     *                 @OA\Property(property="course_type",           type="string", enum={"online","offline"}),
     *                 @OA\Property(property="title",                 type="string", maxLength=255),
     *                 @OA\Property(property="title_for_certificate", type="string", maxLength=255, nullable=true),
     *                 @OA\Property(property="description",           type="string"),
     *                 @OA\Property(property="category_id",           type="integer"),
     *                 @OA\Property(property="intro_video",           type="string", nullable=true),
     *                 @OA\Property(property="price",                 type="number", format="float", nullable=true),
     *                 @OA\Property(property="currency",              type="string", maxLength=10, nullable=true),
     *                 @OA\Property(property="hours",                 type="integer", minimum=1),
     *                 @OA\Property(property="language",              type="string", nullable=true),
     *                 @OA\Property(property="level",                 type="string", nullable=true),
     *                 @OA\Property(property="certificate",           type="boolean"),
     *                 @OA\Property(property="image",                 type="string", format="binary", nullable=true),
     *                 @OA\Property(property="active",                type="boolean", nullable=true),
     *                 @OA\Property(property="outside_materials",     type="boolean", nullable=true),
     *                 @OA\Property(property="is_evaluate",           type="boolean", nullable=true),
     *                 @OA\Property(property="allow_attendances",     type="boolean", nullable=true),
     *                 @OA\Property(property="instructors",           type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="qualification_skill_ids", type="array", @OA\Items(type="integer"), nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Course"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(CourseRequest $request, Course $course): JsonResponse
    {
        $course = $this->courseService->update(
            $course,
            $request->validated(),
            $request->file('image'),
        );

        return $this->success(
            __('messages.updated'),
            new CourseResource($course),
        );
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}",
     *     tags={"Courses"},
     *     summary="Delete a course (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course): JsonResponse
    {
        $this->courseService->delete($course);
        return $this->deleted();
    }
}
