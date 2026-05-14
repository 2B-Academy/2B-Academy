<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Category;
use App\Repositories\Contracts\CategoryRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CategoryService
{
    use HasFile;

    public function __construct(
        private readonly CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function list(int $perPage = 15, ?string $search = null, ?bool $active = null): LengthAwarePaginator
    {
        return $this->categoryRepository->paginateWithFilters($perPage, $search, $active);
    }

    public function allActive(): Collection
    {
        return $this->categoryRepository->allActive();
    }

    public function findOrFail(int $id): Category
    {
        return $this->categoryRepository->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $logo = null): Category
    {
        if ($logo) {
            $data['logo'] = $this->uploadRequestFile('Category', request(), 'logo');
        }
        $data['active'] = (bool) ($data['active'] ?? false);

        return $this->categoryRepository->create($data);
    }

    public function update(Category $category, array $data, ?UploadedFile $logo = null): Category
    {
        if ($logo) {
            $data['logo'] = $this->uploadRequestFile('Category', request(), 'logo');
        }
        $data['active'] = (bool) ($data['active'] ?? false);

        return $this->categoryRepository->update($category, $data);
    }

    public function delete(Category $category): bool
    {
        return $this->categoryRepository->delete($category);
    }
}
