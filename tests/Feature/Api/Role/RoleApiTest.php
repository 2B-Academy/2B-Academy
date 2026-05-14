<?php

namespace Tests\Feature\Api\Role;

use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\Feature\Api\ApiTestCase;

class RoleApiTest extends ApiTestCase
{
    // =========================================================================
    // GET /api/v1/roles  (admin only)
    // =========================================================================

    public function test_index_returns_paginated_roles_for_admin(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/roles');

        $this->assertPaginated($response);
    }

    public function test_index_requires_admin_auth(): void
    {
        $response = $this->getJson(self::BASE . '/roles');
        $this->assertUnauthorized($response);
    }

    public function test_index_forbids_regular_users(): void
    {
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/roles');

        $this->assertForbidden($response);
    }

    // =========================================================================
    // GET /api/v1/roles/all
    // =========================================================================

    public function test_all_returns_full_list(): void
    {
        Role::create(['name' => 'Moderator', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/roles/all');

        $this->assertSuccess($response);
    }

    // =========================================================================
    // POST /api/v1/roles
    // =========================================================================

    public function test_store_creates_role(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/roles', [
            'name' => 'Editor',
        ]);

        $this->assertCreated($response);
        $this->assertDatabaseHas('roles', ['name' => 'Editor']);
    }

    public function test_store_creates_role_with_permissions(): void
    {
        $permission = Permission::create(['name' => 'edit-posts', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/roles', [
            'name'        => 'Writer',
            'permissions' => ['edit-posts'],
        ]);

        $this->assertCreated($response);
        $role = Role::where('name', 'Writer')->first();
        $this->assertTrue($role->hasPermissionTo('edit-posts'));
    }

    public function test_store_requires_name(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/roles', []);

        $this->assertValidationError($response);
    }

    public function test_store_rejects_duplicate_role_name(): void
    {
        Role::create(['name' => 'Duplicate', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/roles', [
            'name' => 'Duplicate',
        ]);

        $this->assertValidationError($response);
    }

    // =========================================================================
    // PUT /api/v1/roles/{id}
    // =========================================================================

    public function test_update_renames_role(): void
    {
        $role = Role::create(['name' => 'OldName', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->putJson(self::BASE . '/roles/' . $role->id, [
            'name' => 'NewName',
        ]);

        $this->assertSuccess($response);
        $this->assertDatabaseHas('roles', ['id' => $role->id, 'name' => 'NewName']);
    }

    // =========================================================================
    // GET /api/v1/roles/{id}
    // =========================================================================

    public function test_show_returns_role_with_permissions(): void
    {
        $role = Role::create(['name' => 'Viewer', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/roles/' . $role->id);

        $this->assertSuccess($response);
        $response->assertJsonPath('result.name', 'Viewer');
        $response->assertJsonStructure(['result' => ['id', 'name', 'permissions']]);
    }

    // =========================================================================
    // DELETE /api/v1/roles/{id}
    // =========================================================================

    public function test_destroy_deletes_role(): void
    {
        $role = Role::create(['name' => 'Temporary', 'guard_name' => 'web']);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->deleteJson(self::BASE . '/roles/' . $role->id);

        $this->assertSuccess($response);
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }
}
