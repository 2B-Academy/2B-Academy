<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AboutRequest;
use App\Http\Requests\Api\TestimonialRequest;
use App\Http\Resources\AboutResource;
use App\Http\Resources\TestimonialResource;
use App\Models\Testimonial;
use App\Services\CmsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class CmsController extends ApiController
{
    public function __construct(private readonly CmsService $service) {}

    // ── About ──────────────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/about",
     *     tags={"CMS"},
     *     summary="Get the About page content. Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="About content",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/About"))
     *             }
     *         )
     *     )
     * )
     */
    public function aboutShow(): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new AboutResource($this->service->getAbout()));
    }

    /**
     * @OA\Post(
     *     path="/about",
     *     tags={"CMS"},
     *     summary="Update the About page content (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="about",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="mission", ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="vision",  ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="goals",   ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="image",   type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP, max 2MB.")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/About"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function aboutUpdate(AboutRequest $request): JsonResponse
    {
        $about = $this->service->updateAbout($request->validated(), $request->file('image'));
        return $this->success(__('messages.updated'), new AboutResource($about));
    }

    // ── Testimonials ───────────────────────────────────────────────────────

    /**
     * @OA\Get(
     *     path="/testimonials",
     *     tags={"CMS"},
     *     summary="List testimonials (paginated). Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(ref="#/components/parameters/Page"),
     *     @OA\Parameter(ref="#/components/parameters/PerPage"),
     *     @OA\Response(
     *         response=200,
     *         description="Paginated testimonials",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Testimonial")
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function testimonialIndex(Request $request): JsonResponse
    {
        $testimonials = $this->service->paginateTestimonials((int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $testimonials);
    }

    /**
     * @OA\Get(
     *     path="/testimonials/active",
     *     tags={"CMS"},
     *     summary="List active testimonials (no pagination). Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Response(
     *         response=200,
     *         description="Active testimonials",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(
     *                     property="result",
     *                     type="array",
     *                     @OA\Items(ref="#/components/schemas/Testimonial")
     *                 ))
     *             }
     *         )
     *     )
     * )
     */
    public function testimonialActiveList(): JsonResponse
    {
        return $this->success(__('messages.retrieved'),
            TestimonialResource::collection($this->service->activeTestimonials())
        );
    }

    /**
     * @OA\Get(
     *     path="/testimonials/{testimonial}",
     *     tags={"CMS"},
     *     summary="Show a testimonial. Public.",
     *     @OA\Parameter(ref="#/components/parameters/AcceptLanguage"),
     *     @OA\Parameter(name="testimonial", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(
     *         response=200,
     *         description="Testimonial",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Testimonial"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function testimonialShow(Testimonial $testimonial): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new TestimonialResource($testimonial));
    }

    /**
     * @OA\Post(
     *     path="/testimonials",
     *     tags={"CMS"},
     *     summary="Create a testimonial (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","description"},
     *                 @OA\Property(property="name",        ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="description", ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="image",       type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP, max 2MB."),
     *                 @OA\Property(property="active",      type="boolean", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Created",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Testimonial"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function testimonialStore(TestimonialRequest $request): JsonResponse
    {
        $testimonial = $this->service->createTestimonial($request->validated(), $request->file('image'));
        return $this->created(__('messages.created'), new TestimonialResource($testimonial));
    }

    /**
     * @OA\Put(
     *     path="/testimonials/{testimonial}",
     *     tags={"CMS"},
     *     summary="Update a testimonial (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="testimonial", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name",        ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="description", ref="#/components/schemas/TranslatedString"),
     *                 @OA\Property(property="image",       type="string", format="binary", nullable=true, description="PNG/JPG/JPEG/WEBP, max 2MB."),
     *                 @OA\Property(property="active",      type="boolean", nullable=true)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Updated",
     *         @OA\JsonContent(
     *             allOf={
     *                 @OA\Schema(ref="#/components/schemas/SuccessResponse"),
     *                 @OA\Schema(@OA\Property(property="result", ref="#/components/schemas/Testimonial"))
     *             }
     *         )
     *     ),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound"),
     *     @OA\Response(response=422, ref="#/components/responses/ValidationError")
     * )
     */
    public function testimonialUpdate(Testimonial $testimonial, TestimonialRequest $request): JsonResponse
    {
        $testimonial = $this->service->updateTestimonial($testimonial, $request->validated(), $request->file('image'));
        return $this->success(__('messages.updated'), new TestimonialResource($testimonial));
    }

    /**
     * @OA\Delete(
     *     path="/testimonials/{testimonial}",
     *     tags={"CMS"},
     *     summary="Delete a testimonial (admin only).",
     *     security={{"BearerAuth": {}}},
     *     @OA\Parameter(name="testimonial", in="path", required=true, @OA\Schema(type="integer", minimum=1)),
     *     @OA\Response(response=200, description="Deleted", @OA\JsonContent(ref="#/components/schemas/EmptyResponse")),
     *     @OA\Response(response=401, ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response=403, ref="#/components/responses/Forbidden"),
     *     @OA\Response(response=404, ref="#/components/responses/NotFound")
     * )
     */
    public function testimonialDestroy(Testimonial $testimonial): JsonResponse
    {
        $this->service->deleteTestimonial($testimonial);
        return $this->deleted(__('messages.deleted'));
    }
}
