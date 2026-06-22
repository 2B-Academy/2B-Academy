<?php

namespace App\Repositories\Eloquents;

use App\Models\About;
use App\Repositories\Contracts\AboutRepositoryInterface;

class AboutRepository extends BaseRepository implements AboutRepositoryInterface
{
    public function __construct(About $model)
    {
        parent::__construct($model);
    }

    public function first(): ?About
    {
        return $this->model->newQuery()->first();
    }

    public function updateOrCreate(array $data): About
    {
        $existing = $this->first();
        return $this->model->newQuery()->updateOrCreate(
            ['id' => $existing?->id ?? null],
            $data
        );
    }
}
