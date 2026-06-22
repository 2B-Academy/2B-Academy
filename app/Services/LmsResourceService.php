<?php

namespace App\Services;

use App\Models\LmsResource;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Storage;

class LmsResourceService
{
    public function list(int $perPage = 15, ?string $search = null, ?string $type = null): LengthAwarePaginator
    {
        return LmsResource::query()
            ->with(['qualificationSkill:id,name', 'createdByAdmin:id,name'])
            ->when($search, fn ($q) => $q->where('title', 'LIKE', "%{$search}%"))
            ->when($type, fn ($q) => $q->where('type', $type))
            ->latest()
            ->paginate($perPage);
    }

    public function show(LmsResource $resource): LmsResource
    {
        return $resource->load(['qualificationSkill:id,name', 'createdByAdmin:id,name']);
    }

    public function create(array $data, ?int $adminId, ?UploadedFile $file = null): LmsResource
    {
        $payload = $this->buildPayload($data, $adminId, $file);

        return LmsResource::create($payload);
    }

    public function update(LmsResource $resource, array $data, ?UploadedFile $file = null): LmsResource
    {
        // Delete old file if a new one is being uploaded
        if ($file && $resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        $payload = $this->buildPayload($data, $resource->created_by_admin_id, $file);

        // If type changed away from file, clean up old stored file
        if (isset($payload['type']) && $payload['type'] !== 'file' && $resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
            $payload['file_path'] = null;
            $payload['file_name'] = null;
            $payload['file_size'] = null;
        }

        $resource->update($payload);

        return $resource->fresh()->load('qualificationSkill:id,name');
    }

    public function delete(LmsResource $resource): bool
    {
        if ($resource->file_path) {
            Storage::disk('public')->delete($resource->file_path);
        }

        return $resource->delete();
    }

    /**
     * Build the attribute payload, handling file upload when present.
     */
    private function buildPayload(array $data, ?int $adminId, ?UploadedFile $file): array
    {
        $payload = [
            'title'                  => $data['title'],
            'type'                   => $data['type'],
            'content'                => $data['content'] ?? null,
            'url'                    => $data['url'] ?? null,
            'qualification_skill_id' => $data['qualification_skill_id'] ?? null,
            'created_by_admin_id'    => $adminId,
        ];

        if ($file) {
            $storedPath = $file->store('lms-resources', 'public');

            $payload['file_path'] = $storedPath;
            $payload['file_name'] = $file->getClientOriginalName();
            $payload['file_size'] = $file->getSize();
        }

        return $payload;
    }
}
