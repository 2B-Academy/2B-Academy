<?php

namespace App\Repositories\Contracts;

use App\Models\About;

interface AboutRepositoryInterface extends BaseRepositoryInterface
{
    public function first(): ?About;
    public function updateOrCreate(array $data): About;
}
