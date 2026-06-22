<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\About;
use App\Models\Testimonial;
use App\Repositories\Contracts\AboutRepositoryInterface;
use App\Repositories\Contracts\TestimonialRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CmsService
{
    use HasFile;

    public function __construct(
        private readonly AboutRepositoryInterface       $aboutRepo,
        private readonly TestimonialRepositoryInterface $testimonialRepo
    ) {}

    // ── About ──────────────────────────────────────────────────────────────

    public function getAbout(): About
    {
        return $this->aboutRepo->first() ?? new About();
    }

    public function updateAbout(array $data, $imageFile = null): About
    {
        if ($imageFile) {
            $data['image'] = $this->uploadRequestFile('About', request(), null, $imageFile);
        }
        return $this->aboutRepo->updateOrCreate($data);
    }

    // ── Testimonials ───────────────────────────────────────────────────────

    public function paginateTestimonials(int $perPage): LengthAwarePaginator
    {
        return $this->testimonialRepo->paginateLatest($perPage);
    }

    public function activeTestimonials(): Collection
    {
        return $this->testimonialRepo->allActive();
    }

    public function findTestimonial(int $id): Testimonial
    {
        /** @var Testimonial */
        return $this->testimonialRepo->findOrFail($id);
    }

    public function createTestimonial(array $data, $imageFile = null): Testimonial
    {
        if ($imageFile) {
            $data['image'] = $this->uploadRequestFile('Testimonial', request(), null, $imageFile);
        }
        $data['active'] = (bool) ($data['active'] ?? true);

        /** @var Testimonial */
        return $this->testimonialRepo->create($data);
    }

    public function updateTestimonial(Testimonial $testimonial, array $data, $imageFile = null): Testimonial
    {
        if ($imageFile) {
            $data['image'] = $this->uploadRequestFile('Testimonial', request(), null, $imageFile);
        }
        $data['active'] = (bool) ($data['active'] ?? $testimonial->active);

        /** @var Testimonial */
        return $this->testimonialRepo->update($testimonial, $data);
    }

    public function deleteTestimonial(Testimonial $testimonial): void
    {
        $this->testimonialRepo->delete($testimonial);
    }
}
