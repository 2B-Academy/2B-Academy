<?php

namespace Tests\Feature\Api;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

abstract class ApiTestCase extends TestCase
{
    use RefreshDatabase;

    protected const BASE = '/api/v1';

    // -------------------------------------------------------------------------
    // Auth helpers — create model + real Sanctum token (our middleware reads it)
    // -------------------------------------------------------------------------

    protected function adminToken(?Admin $admin = null): array
    {
        $admin ??= Admin::factory()->create();
        $token   = $admin->createToken('test')->plainTextToken;

        return ['model' => $admin, 'headers' => ['Authorization' => 'Bearer ' . $token]];
    }

    protected function userToken(?User $user = null): array
    {
        $user  ??= User::factory()->create();
        $token   = $user->createToken('test')->plainTextToken;

        return ['model' => $user, 'headers' => ['Authorization' => 'Bearer ' . $token]];
    }

    // -------------------------------------------------------------------------
    // Assertion helpers
    // -------------------------------------------------------------------------

    protected function assertSuccess(TestResponse $response, int $status = 200): void
    {
        $response->assertStatus($status)
                 ->assertJsonStructure(['status', 'message'])
                 ->assertJson(['status' => 'success']);
    }

    protected function assertCreated(TestResponse $response): void
    {
        $this->assertSuccess($response, 201);
    }

    protected function assertPaginated(TestResponse $response): void
    {
        $response->assertStatus(200)
                 ->assertJsonStructure(['status', 'message', 'result', 'meta' => [
                     'current_page', 'last_page', 'per_page', 'total',
                 ]])
                 ->assertJson(['status' => 'success']);
    }

    protected function assertUnauthorized(TestResponse $response): void
    {
        $response->assertStatus(401)->assertJson(['status' => 'error']);
    }

    protected function assertForbidden(TestResponse $response): void
    {
        $response->assertStatus(403)->assertJson(['status' => 'error']);
    }

    protected function assertNotFound(TestResponse $response): void
    {
        $response->assertStatus(404);
    }

    protected function assertValidationError(TestResponse $response): void
    {
        $response->assertStatus(422);
    }
}
