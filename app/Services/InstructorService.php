<?php

namespace App\Services;

use App\Http\Traits\HasFile;
use App\Models\Instructor;
use App\Repositories\Contracts\InstructorRepositoryInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class InstructorService
{
    use HasFile;

    public function __construct(
        private readonly InstructorRepositoryInterface $instructorRepository,
    ) {}

    public function list(int $perPage = 15, ?string $search = null): LengthAwarePaginator
    {
        return $this->instructorRepository->paginateWithSearch($perPage, $search);
    }

    public function all(): Collection
    {
        return $this->instructorRepository->all();
    }

    public function findOrFail(int $id): Instructor
    {
        return $this->instructorRepository->findOrFail($id);
    }

    public function create(array $data, ?UploadedFile $image = null): Instructor
    {
        if ($image) {
            $data['image'] = $this->uploadRequestFile('Instructor', request(), 'image');
        }
        return $this->instructorRepository->create($data);
    }

    public function update(Instructor $instructor, array $data, ?UploadedFile $image = null): Instructor
    {
        if ($image) {
            $data['image'] = $this->uploadRequestFile('Instructor', request(), 'image');
        }
        return $this->instructorRepository->update($instructor, $data);
    }

    public function delete(Instructor $instructor): bool
    {
        return $this->instructorRepository->delete($instructor);
    }
}
