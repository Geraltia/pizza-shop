<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminProductControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected function authenticateAdmin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);
        return $admin;
    }

    public function test_products_returns_paginated_products()
    {
        $this->authenticateAdmin();

        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/admin/products?per_page=5&page=1');

        $response->assertJsonStructure([
            'data',
            'links',
        ]);

    }

    public function test_addProduct_creates_product()
    {
        $this->authenticateAdmin();

        $data = [
            'name' => 'Test Product',
            'type' => 'pizza',
            'price' => 9.99,
        ];

        $response = $this->postJson('/api/admin/products', $data);

        $response->assertStatus(201)
            ->assertJsonFragment(['name' => 'Test Product']);
        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_updateProduct_updates_existing_product()
    {
        $this->authenticateAdmin();

        $product = Product::factory()->create();

        $data = [
            'name' => 'Updated Name',
            'type' => 'drink',
            'price' => 5.55,
        ];

        $response = $this->putJson("/api/admin/products/{$product->id}", $data);

        $response->assertStatus(200)
            ->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_deleteProduct_removes_product()
    {
        $this->authenticateAdmin();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }
}
