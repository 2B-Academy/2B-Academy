<?php

namespace App\Repositories\Contracts;

interface SettingRepositoryInterface extends BaseRepositoryInterface
{
    public function updateByKey(string $key, mixed $value): void;
}
