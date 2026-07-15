<?php

namespace App\Http\Controllers\apis;

use App\Http\Requests\Api\BlogRequest;
use App\Http\Resources\Admin\AdminBlogResource;
use App\Http\Resources\BlogListResource;
use App\Http\Resources\BlogResource;
use App\Models\Blog;
use App\Services\BlogService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogController extends ApiController
{
    public function __construct(private readonly BlogService $service) {}

    /*
    |--------------------------------------------------------------------------
    | Public (website) — published blogs only
    |--------------------------------------------------------------------------
    */

    /**
     * Paginated list of published blogs.
     * Filters: search, level, qualification_skill_id, qualification_ids[] (tailored).
     */
    public function index(Request $request): JsonResponse
    {
        $blogs = $this->service->paginate(
            perPage: (int) $request->get('per_page', 9),
            filters: [
                'search'                 => $request->get('search'),
                'level'                  => $request->get('level'),
                'qualification_skill_id' => $request->get('qualification_skill_id'),
                'qualification_ids'      => $this->arrayFilter($request->get('qualification_ids')),
                'only_published'         => true,
            ],
        );

        return $this->paginated(__('messages.retrieved'), BlogListResource::collection($blogs));
    }

    /**
     * A single published blog by slug (with sections + author bio).
     */
    public function show(string $slug): JsonResponse
    {
        $blog = $this->service->findBySlug($slug, onlyPublished: true);

        if (! $blog) {
            return $this->notFound(__('messages.not_found'));
        }

        return $this->success(__('messages.retrieved'), new BlogResource($blog));
    }

    /**
     * Related published blogs for the reading page rail.
     */
    public function related(string $slug): JsonResponse
    {
        $blog = $this->service->findBySlug($slug, onlyPublished: true);

        if (! $blog) {
            return $this->notFound(__('messages.not_found'));
        }

        return $this->success(
            __('messages.retrieved'),
            BlogListResource::collection($this->service->related($blog))
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Admin (dashboard) — full management, includes drafts
    |--------------------------------------------------------------------------
    */

    public function adminIndex(Request $request): JsonResponse
    {
        $blogs = $this->service->paginate(
            perPage: (int) $request->get('per_page', 15),
            filters: [
                'search'                 => $request->get('search'),
                'level'                  => $request->get('level'),
                'qualification_skill_id' => $request->get('qualification_skill_id'),
                'only_published'         => false,
            ],
        );

        return $this->paginated(__('messages.retrieved'), BlogListResource::collection($blogs));
    }

    public function adminShow(Blog $blog): JsonResponse
    {
        return $this->success(__('messages.retrieved'), new AdminBlogResource($this->service->findForEdit($blog)));
    }

    public function store(BlogRequest $request): JsonResponse
    {
        $blog = $this->service->create($request);

        return $this->created(__('messages.created'), new AdminBlogResource($blog));
    }

    public function update(Blog $blog, BlogRequest $request): JsonResponse
    {
        $updated = $this->service->update($blog, $request);

        return $this->success(__('messages.updated'), new AdminBlogResource($updated));
    }

    public function destroy(Blog $blog): JsonResponse
    {
        $this->service->delete($blog);

        return $this->deleted(__('messages.deleted'));
    }

    /**
     * Normalise a repeated query param (?qualification_ids[]=1&...) to int[].
     *
     * @return array<int>|null
     */
    private function arrayFilter($value): ?array
    {
        if ($value === null || $value === '') {
            return null;
        }

        $values = is_array($value) ? $value : explode(',', (string) $value);

        return array_values(array_filter(array_map('intval', $values)));
    }
}
