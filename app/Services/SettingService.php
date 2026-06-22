<?php

namespace App\Services;

use App\Repositories\Contracts\SettingRepositoryInterface;
use Illuminate\Support\Facades\Cache;

class SettingService
{
    public function __construct(
        private readonly SettingRepositoryInterface $settingRepository,
    ) {}

    /** Return settings as a plain key => value map (non-file types only for public). */
    public function getMap(): array
    {
        return $this->settingRepository->all()
            ->where('type', '!=', 'file')
            ->pluck('value', 'key')
            ->all();
    }

    /** Return full settings collection for admin editing. */
    public function getAll(): \Illuminate\Database\Eloquent\Collection
    {
        return $this->settingRepository->all();
    }

    public function updateMany(array $settings): void
    {
        foreach ($settings as $key => $value) {
            $this->settingRepository->updateByKey((string) $key, $value);
        }

        Cache::forget('cms.settings.map');
        // Mobile thresholds (academy/attendance/rating/...) are cached
        // separately under `mobile.settings.map`. Without this, edits to a
        // `mobile_*` setting from the admin panel would lag up to 10 minutes.
        Cache::forget('mobile.settings.map');
        // Cross-module map (e.g. the attendance passcode mode + reset interval
        // live in Platform Config but are read by the mobile/passcode layer).
        // Flush it so a saved "Course Attendance" / "Passcode Reset" change
        // takes effect on the very next dashboard refresh.
        Cache::forget('settings.all.map');
    }
}
