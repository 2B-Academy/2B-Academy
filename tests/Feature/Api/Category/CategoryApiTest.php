<?php

namespace Tests\Feature\Api\Category;

use App\Models\Category;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\Feature\Api\ApiTestCase;

class CategoryApiTest extends ApiTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');
    }

    // =========================================================================
    // GET /api/v1/categories  (admin-only paginated list)
    // =========================================================================

    public function test_index_returns_paginated_categories_for_admin(): void
    {
        Category::factory()->count(3)->create();
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/categories');

        $this->assertPaginated($response);
    }

    public function test_index_requires_admin_auth(): void
    {
        $response = $this->getJson(self::BASE . '/categories');
        $this->assertUnauthorized($response);
    }

    public function test_index_forbids_regular_users(): void
    {
        ['headers' => $headers] = $this->userToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/categories');

        $this->assertForbidden($response);
    }

    public function test_index_filters_by_search(): void
    {
        Category::factory()->create(['name' => json_encode(['ar' => 'برمجة', 'en' => 'programming'])]);
        Category::factory()->create(['name' => json_encode(['ar' => 'تصميم', 'en' => 'design'])]);
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/categories?search=design');

        $this->assertPaginated($response);
        $response->assertJsonPath('meta.total', 1);
    }

    // =========================================================================
    // GET /api/v1/categories/active  (public)
    // =========================================================================

    public function test_active_list_is_public(): void
    {
        Category::factory()->count(2)->create();
        Category::factory()->inactive()->create();

        $response = $this->getJson(self::BASE . '/categories/active');

        $this->assertSuccess($response);
        $this->assertCount(2, $response->json('result'));
    }

    // =========================================================================
    // GET /api/v1/categories/{id}
    // =========================================================================

    public function test_show_returns_category_for_admin(): void
    {
        $category = Category::factory()->create();
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/categories/' . $category->id);

        $this->assertSuccess($response);
        $response->assertJsonPath('result.id', $category->id);
    }

    public function test_show_returns_404_for_unknown_id(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->getJson(self::BASE . '/categories/99999');

        $this->assertNotFound($response);
    }

    // =========================================================================
    // POST /api/v1/categories
    // =========================================================================

    public function test_store_creates_category(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/categories', [
            'name'   => ['en' => 'Programming', 'ar' => 'برمجة'],
            'active' => true,
        ]);

        $this->assertCreated($response);
        $this->assertDatabaseHas('categories', ['active' => true]);
    }

    public function test_store_succeeds_without_logo(): void
    {
        // Categories no longer carry an image (removed per design review),
        // so a create with no logo must succeed.
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/categories', [
            'name' => ['en' => 'No Logo Category', 'ar' => 'تصنيف بدون شعار'],
        ]);

        $this->assertCreated($response);
    }

    public function test_store_requires_name(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->postJson(self::BASE . '/categories', [
            'active' => true,
        ]);

        $this->assertValidationError($response);
    }

    // =========================================================================
    // PUT /api/v1/categories/{id}
    // =========================================================================

    public function test_update_modifies_category(): void
    {
        $category = Category::factory()->create();
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->putJson(self::BASE . '/categories/' . $category->id, [
            'name'   => 'Updated Name',
            'active' => false,
        ]);

        $this->assertSuccess($response);
        $this->assertDatabaseHas('categories', ['id' => $category->id, 'active' => false]);
    }

    public function test_update_without_logo_keeps_existing_logo(): void
    {
        $category = Category::factory()->create(['logo' => 'Category/original.jpg']);
        ['headers' => $headers] = $this->adminToken();

        $this->withHeaders($headers)->putJson(self::BASE . '/categories/' . $category->id, [
            'name' => 'New Name',
        ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id, 'logo' => 'Category/original.jpg']);
    }

    // =========================================================================
    // DELETE /api/v1/categories/{id}
    // =========================================================================

    public function test_destroy_deletes_category(): void
    {
        $category = Category::factory()->create();
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->deleteJson(self::BASE . '/categories/' . $category->id);

        $this->assertSuccess($response);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    public function test_destroy_returns_404_for_unknown_id(): void
    {
        ['headers' => $headers] = $this->adminToken();

        $response = $this->withHeaders($headers)->deleteJson(self::BASE . '/categories/99999');

        $this->assertNotFound($response);
    }
}
