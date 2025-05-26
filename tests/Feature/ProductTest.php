<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase;

    public function test_product_can_be_created()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'product' => ['id', 'name', 'description', 'price', 'created_at', 'updated_at'],
            ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'description' => 'Test Description',
            'price' => 99.99,
        ]);
    }

    public function test_product_creation_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/products', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'price']);
    }

    public function test_product_creation_fails_with_invalid_price()
    {
        $response = $this->postJson('/api/products', [
            'name' => 'Test Product',
            'price' => 'not-a-number',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }
}
