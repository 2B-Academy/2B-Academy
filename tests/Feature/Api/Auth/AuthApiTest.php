<?php

namespace Tests\Feature\Api\Auth;

use App\Models\Admin;
use App\Models\User;
use Tests\Feature\Api\ApiTestCase;

class AuthApiTest extends ApiTestCase
{
    // =========================================================================
    // User Auth
    // =========================================================================

    public function test_user_login_with_valid_credentials_returns_token(): void
    {
        $user = User::factory()->create(['password' => bcrypt('secret123')]);

        $response = $this->postJson(self::BASE . '/auth/user/login', [
            'email'    => $user->email,
            'password' => 'secret123',
        ]);

        $this->assertSuccess($response);
        $response->assertJsonStructure(['result' => ['token', 'user']]);
    }

    public function test_user_login_with_wrong_password_returns_401(): void
    {
        $user = User::factory()->create(['password' => bcrypt('correct')]);

        $response = $this->postJson(self::BASE . '/auth/user/login', [
            'email'    => $user->email,
            'password' => 'wrong-password',
        ]);

        $response->assertStatus(401)->assertJson(['status' => 'error']);
    }

    public function test_user_login_validation_fails_without_email(): void
    {
        $response = $this->postJson(self::BASE . '/auth/user/login', [
            'password' => 'secret',
        ]);

        $this->assertValidationError($response);
    }

    public function test_user_me_returns_profile(): void
    {
        ['headers' => $headers, 'model' => $user] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/auth/user/me');

        $this->assertSuccess($response);
        $response->assertJsonPath('result.email', $user->email);
    }

    public function test_user_me_without_token_returns_401(): void
    {
        $response = $this->getJson(self::BASE . '/auth/user/me');
        $this->assertUnauthorized($response);
    }

    public function test_user_cannot_access_admin_me(): void
    {
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/auth/admin/me');

        $this->assertForbidden($response);
    }

    public function test_user_logout_invalidates_token(): void
    {
        ['headers' => $headers] = $this->userToken();

        $logout = $this->withHeaders($headers)->postJson(self::BASE . '/auth/user/logout');
        $this->assertSuccess($logout);

        // Same token should now be rejected
        $me = $this->withHeaders($headers)->getJson(self::BASE . '/auth/user/me');
        $this->assertUnauthorized($me);
    }

    public function test_user_logout_all_invalidates_all_tokens(): void
    {
        $user = User::factory()->create();
        $token1 = $user->createToken('device-1')->plainTextToken;
        $token2 = $user->createToken('device-2')->plainTextToken;

        $this->withHeaders(['Authorization' => 'Bearer ' . $token1])
             ->postJson(self::BASE . '/auth/user/logout-all');

        // Both tokens should now be invalid
        $response = $this->withHeaders(['Authorization' => 'Bearer ' . $token2])
                         ->getJson(self::BASE . '/auth/user/me');
        $this->assertUnauthorized($response);
    }

    // =========================================================================
    // Admin Auth
    // =========================================================================

    public function test_admin_login_with_valid_credentials_returns_token(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('admin123')]);

        $response = $this->postJson(self::BASE . '/auth/admin/login', [
            'email'    => $admin->email,
            'password' => 'admin123',
        ]);

        $this->assertSuccess($response);
        $response->assertJsonStructure(['result' => ['token', 'admin']]);
    }

    public function test_admin_login_with_wrong_password_returns_401(): void
    {
        $admin = Admin::factory()->create(['password' => bcrypt('correct')]);

        $response = $this->postJson(self::BASE . '/auth/admin/login', [
            'email'    => $admin->email,
            'password' => 'wrong',
        ]);

        $response->assertStatus(401)->assertJson(['status' => 'error']);
    }

    public function test_admin_me_returns_profile(): void
    {
        ['headers' => $headers, 'model' => $admin] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/auth/admin/me');

        $this->assertSuccess($response);
        $response->assertJsonPath('result.email', $admin->email);
    }

    public function test_admin_cannot_access_user_me(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/auth/user/me');

        $this->assertForbidden($response);
    }

    public function test_admin_logout_invalidates_token(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $logout = $this->withHeaders($headers)->postJson(self::BASE . '/auth/admin/logout');
        $this->assertSuccess($logout);

        $me = $this->withHeaders($headers)->getJson(self::BASE . '/auth/admin/me');
        $this->assertUnauthorized($me);
    }
}
