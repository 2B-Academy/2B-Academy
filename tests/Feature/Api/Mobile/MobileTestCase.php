<?php

namespace Tests\Feature\Api\Mobile;

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\MobileSettingSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

/**
 * Base class for the mobile (NAS Employee app) API feature tests.
 *
 * The mobile surface is a server-to-server contract:
 *   • `mobile.token`    — a shared bearer token (settings table)
 *   • `mobile.employee` — an `Employee-Code` header resolved to
 *                         users.machine_code
 *
 * setUp() seeds every mobile platform setting, pins the shared token to
 * a deterministic value, and flushes the MobileSettings cache so each
 * test runs against a known configuration.
 */
abstract class MobileTestCase extends TestCase
{
    use RefreshDatabase;

    protected const BASE  = '/api/v1';
    protected const TOKEN = 'TEST-MOBILE-SHARED-TOKEN';

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(MobileSettingSeeder::class);

        // Pin the shared bearer token so the auth header is predictable.
        Setting::updateOrCreate(
            ['key' => 'mobile_shared_bearer_token', 'module' => 'mobile_security'],
            ['type' => 'string', 'label' => 'Mobile shared token', 'value' => self::TOKEN],
        );

        // MobileSettings memoizes the settings map in the cache.
        Cache::flush();
    }

    // -------------------------------------------------------------------------
    // Employee + header helpers
    // -------------------------------------------------------------------------

    /** Create an employee (learner) with a guaranteed machine_code. */
    protected function employee(array $attributes = []): User
    {
        return User::factory()->create($attributes);
    }

    /** Full mobile headers (shared token + employee code) for a user. */
    protected function headersFor(User $user, string $locale = 'en'): array
    {
        return [
            'X-Api-Token'     => self::TOKEN,
            'Employee-Code'   => $user->machine_code,
            'Accept-Language' => $locale,
            'Accept'          => 'application/json',
        ];
    }

    /** Only the shared token, no employee code. */
    protected function tokenOnlyHeaders(string $locale = 'en'): array
    {
        return [
            'X-Api-Token'     => self::TOKEN,
            'Accept-Language' => $locale,
            'Accept'          => 'application/json',
        ];
    }

    // -------------------------------------------------------------------------
    // Assertion helpers (mobile envelope: status / message / result)
    // -------------------------------------------------------------------------

    protected function assertSuccess(TestResponse $response, int $status = 200): void
    {
        $response->assertStatus($status)
                 ->assertJsonStructure(['status', 'message'])
                 ->assertJson(['status' => 'success']);
    }

    protected function assertPaginated(TestResponse $response): void
    {
        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'status', 'message', 'result',
                     'meta' => ['current_page', 'last_page', 'per_page', 'total'],
                 ])
                 ->assertJson(['status' => 'success']);
    }

    protected function assertError(TestResponse $response, int $status): void
    {
        $response->assertStatus($status)->assertJson(['status' => 'error']);
    }
}
