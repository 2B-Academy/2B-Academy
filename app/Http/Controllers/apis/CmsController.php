<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\AboutRequest;
use App\Http\Requests\Api\TestimonialRequest;
use App\Http\Resources\AboutResource;
use App\Http\Resources\TestimonialResource;
use App\Http\Traits\HasFile;
use App\Models\About;
use App\Models\Testimonial;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CmsController extends ApiController
{
    use HasFile;

    // ── About ──────────────────────────────────────────────────────────────

    public function aboutShow(): JsonResponse
    {
        $about = About::first() ?? new About();
        return $this->success(__('messages.retrieved'), new AboutResource($about));
    }

    public function aboutUpdate(AboutRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile('About', $request->file('image'));
        }
        $about = About::updateOrCreate(['id' => About::first()?->id ?? null], $data);
        return $this->success(__('messages.updated'), new AboutResource($about));
    }

    // ── Testimonials ───────────────────────────────────────────────────────

    public function testimonialIndex(Request $request): JsonResponse
    {
        $testimonials = Testimonial::orderByDesc('id')->paginate((int) $request->get('per_page', 20));
        return $this->paginated(__('messages.retrieved'), $testimonials);
    }

    public function testimonialActiveList(): JsonResponse
    {
        $testimonials = Testimonial::active()->get();
        return $this->success(__('messages.retrieved'), TestimonialResource::collection($testimonials));
    }

    public function testimonialShow(Testimonial $testimonial): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new TestimonialResource($testimonial));
    }

    public function testimonialStore(TestimonialRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile('Testimonial', $request->file('image'));
        }
        $data['active'] = (bool) ($data['active'] ?? true);
        $testimonial = Testimonial::create($data);
        return $this->created(__('messages.created'), new TestimonialResource($testimonial));
    }

    public function testimonialUpdate(Testimonial $testimonial, TestimonialRequest $request): JsonResponse
    {
        $data = $request->validated();
        if ($request->hasFile('image')) {
            $data['image'] = $this->uploadFile('Testimonial', $request->file('image'));
        }
        $data['active'] = (bool) ($data['active'] ?? $testimonial->active);
        $testimonial->update($data);
        return $this->success(__('messages.updated'), new TestimonialResource($testimonial->fresh()));
    }

    public function testimonialDestroy(Testimonial $testimonial): JsonResponse
    {
        $testimonial->delete();
        return $this->deleted(__('messages.deleted'));
    }
}
