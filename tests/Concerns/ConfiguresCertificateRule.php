<?php

namespace Tests\Concerns;

use App\Models\Setting;
use App\Services\CertificatePolicy;

/**
 * Sets the certificate rule the way an admin does — through Platform Config.
 *
 * The rule used to be per-course columns (`certificate_mode`,
 * `certificate_*_threshold`); it now lives in `settings` and is read by
 * App\Services\CertificatePolicy. Tests configure it here so they exercise
 * the same path the admin screen writes to.
 */
trait ConfiguresCertificateRule
{
    protected function configureCertificateRule(
        string $basis,
        ?int $minAttendance = null,
        ?int $minScore = null,
    ): void {
        $this->putSetting(CertificatePolicy::KEY_BASIS, $basis, 'text');

        if ($minAttendance !== null) {
            $this->putSetting(CertificatePolicy::KEY_MIN_ATTENDANCE, (string) $minAttendance, 'number');
        }
        if ($minScore !== null) {
            $this->putSetting(CertificatePolicy::KEY_MIN_SCORE, (string) $minScore, 'number');
        }

        // The policy is a singleton that memoizes its settings snapshot.
        app(CertificatePolicy::class)->refresh();
    }

    private function putSetting(string $key, string $value, string $type): void
    {
        Setting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'type' => $type, 'label' => $key, 'module' => 'platform'],
        );
    }
}
