<?php

namespace App\Repositories\Eloquents;

use App\Models\Setting;
use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class SettingRepository extends BaseRepository implements SettingRepositoryInterface
{
    public function __construct(Setting $model)
    {
        parent::__construct($model);
    }

    public function all(): Collection
    {
        return $this->model->newQuery()->get();
    }

    /**
     * Upsert by key — used by /admin/settings PUT.
     *
     * Platform Settings adds brand-new keys (platform_name, default_language,
     * cohort sizes, certificate award basis, …) that may not exist in the seed
     * on older deployments. If the row exists we update only its value (so we
     * never clobber its `type` or `module`); if it's missing we create a new
     * `platform` text row so the change isn't silently lost.
     */
    public function updateByKey(string $key, mixed $value): void
    {
        $row = $this->model->newQuery()->firstOrNew(['key' => $key]);
        $row->value = is_null($value) ? null : (string) $value;
        if (!$row->exists) {
            $row->type   = 'text';
            $row->module = 'platform';
            $row->label  = $key; // settings.label is NOT NULL on this schema
        }
        $row->save();
    }
}
