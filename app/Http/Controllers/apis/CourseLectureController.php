<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\CourseLectureRequest;
use App\Http\Resources\CourseLectureResource;
use App\Http\Resources\CourseSectionResource;
use App\Http\Traits\HasFile;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Services\CourseLectureService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use OpenApi\Annotations as OA;

class CourseLectureController extends ApiController
{
    use HasFile;

    public function __construct(private readonly CourseLectureService $service) {}

    /**
     * @OA\Get(
     *     path="/courses/{course}/lectures",
     *     tags={"Course Lectures"},
     *     summary="List lectures for a course, grouped by section.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Sections with nested lectures",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseSection")
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
        $sections = $this->service->listForCourse($course);
        return $this->success(__('messages.retrieved'), CourseSectionResource::collection($sections));
    }

    /**
     * @OA\Get(
     *     path="/courses/{course}/modules",
     *     tags={"Course Lectures"},
     *     summary="Flat list of modules (lectures) for the admin Content tab.",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Flat list of modules",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/CourseLecture")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function indexFlat(Course $course): JsonResponse
    {
        $lectures = $this->service->listFlatForCourse($course);
        return $this->success(__('messages.retrieved'), CourseLectureResource::collection($lectures));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/lectures",
     *     tags={"Course Lectures"},
     *     summary="Create a lecture under a course section (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         description="Course id",
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"section_id","title","type","video"},
     *             @OA\Property(property="section_id", type="integer"),
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="type",       type="string", enum={"url","file"}),
     *             @OA\Property(property="video",      type="string", description="External URL or stored file path.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseLecture"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function store(Course $course, CourseLectureRequest $request): JsonResponse
    {
        $lecture = $this->service->create($course, $request->validated());
        return $this->created(__('messages.created'), new CourseLectureResource($lecture));
    }

    /**
     * @OA\Put(
     *     path="/courses/{course}/lectures/{lecture}",
     *     tags={"Course Lectures"},
     *     summary="Update a lecture (admin only).",
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
     *             required={"section_id","title","type","video"},
     *             @OA\Property(property="section_id", type="integer"),
     *             @OA\Property(property="title",      ref="#/components/schemas/TranslatedString"),
     *             @OA\Property(property="type",       type="string", enum={"url","file"}),
     *             @OA\Property(property="video",      type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/CourseLecture"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function update(Course $course, CourseLecture $lecture, CourseLectureRequest $request): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);
        $updated = $this->service->update($lecture, $request->validated());
        return $this->success(__('messages.updated'), new CourseLectureResource($updated));
    }

    /**
     * @OA\Delete(
     *     path="/courses/{course}/lectures/{lecture}",
     *     tags={"Course Lectures"},
     *     summary="Delete a lecture (admin only).",
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
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function destroy(Course $course, CourseLecture $lecture): JsonResponse
    {
        abort_if($lecture->course_id !== $course->id, 404);
        $this->service->delete($lecture);
        return $this->deleted(__('messages.deleted'));
    }

    /**
     * @OA\Post(
     *     path="/courses/{course}/lectures/upload",
     *     tags={"Course Lectures"},
     *     summary="Upload a document file for a module (returns storage path + metadata).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(
     *         name="course", in="path", required=true,
     *         @OA\Schema(type="integer", minimum=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(property="file", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Uploaded",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="object",
     *                     @OA\Property(property="path", type="string"),
     *                     @OA\Property(property="url",  type="string"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="size", type="integer")
     *                 ))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function uploadFile(Course $course, Request $request): JsonResponse
    {
        $request->validate([
            'file' => 'required|file|max:51200|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,txt,jpg,jpeg,png,gif,webp',
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $size = $file->getSize();
        $path = $this->uploadRequestFile('course-lectures', $request, 'file');

        return $this->success(__('messages.created'), [
            'path' => $path,
            'url'  => $this->getFileUrl($path),
            'name' => $originalName,
            'size' => $size,
        ]);
    }
}
