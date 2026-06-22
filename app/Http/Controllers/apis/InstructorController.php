<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\InstructorRequest;
use App\Http\Resources\InstructorResource;
use App\Models\Instructor;
use App\Services\InstructorService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class InstructorController extends ApiController
{
    public function __construct(private readonly InstructorService $instructorService) {}

    /**
     * @OA\Get(
     *     path="/instructors",
     *     tags={"Instructors"},
     *     summary="List instructors (paginated, admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Parameter(ref="#/components/parameters/Search"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated instructors",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Instructor")
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
        $instructors = $this->instructorService->list(
            perPage: (int) $request->get('per_page', 15),
            search:  $request->get('search'),
        );

        return $this->paginated(__('messages.retrieved'), InstructorResource::collection($instructors));
    }

    /**
     * @OA\Get(
     *     path="/instructors/all",
     *     tags={"Instructors"},
     *     summary="List ALL instructors (no pagination). Public — for course creation dropdowns.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="All instructors",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Instructor")
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function all(): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            InstructorResource::collection($this->instructorService->all()),
        );
    }

    /**
     * @OA\Get(
     *     path="/instructors/{instructor}",
     *     tags={"Instructors"},
     *     summary="Show an instructor (with courses_count).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="instructor", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Instructor",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Instructor"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function show(Instructor $instructor): JsonResponse
    {
        return $this->success(
            __('messages.retrieved'),
            new InstructorResource($instructor->loadCount('courses')),
        );
    }

    /**
     * @OA\Post(
     *     path="/instructors",
     *     tags={"Instructors"},
     *     summary="Create an instructor (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","image"},
     *                 @OA\Property(property="name",  ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="bio",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="image", type="string", format="binary", description="PNG/JPG/JPEG/WEBP, max 2MB.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Instructor"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(InstructorRequest $request): JsonResponse
    {
        $instructor = $this->instructorService->create(
            $request->validated(),
            $request->file('image'),
        );

        return $this->created(
            __('messages.created'),
            new InstructorResource($instructor),
        );
    }

    /**
     * @OA\Put(
     *     path="/instructors/{instructor}",
     *     tags={"Instructors"},
     *     summary="Update an instructor (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="instructor", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name",  ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="bio",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="image", type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP, max 2MB.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Instructor"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(InstructorRequest $request, Instructor $instructor): JsonResponse
    {
        $instructor = $this->instructorService->update(
            $instructor,
            $request->validated(),
            $request->file('image'),
        );

        return $this->success(
            __('messages.updated'),
            new InstructorResource($instructor),
        );
    }

    /**
     * @OA\Delete(
     *     path="/instructors/{instructor}",
     *     tags={"Instructors"},
     *     summary="Delete an instructor (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="instructor", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Instructor $instructor): JsonResponse
    {
        $this->instructorService->delete($instructor);
        return $this->deleted();
    }
}
