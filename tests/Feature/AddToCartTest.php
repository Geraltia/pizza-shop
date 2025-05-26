<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
class AddToCartTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    public function test_user_can_add_pizza_within_limit()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'pizza']);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 5,
            ])
            ->assertStatus(200)
            ->assertJson(['message' => 'Товар добавлен в корзину.']);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }

    public function test_user_cannot_add_more_than_10_pizzas()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'pizza']);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 8,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 3,
            ])
            ->assertStatus(400)
            ->assertJson(['error' => 'Можно добавить не более 10 пицц.']);
    }

    public function test_user_cannot_add_more_than_20_drinks()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'drink']);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 19,
        ]);

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 2,
            ])
            ->assertStatus(400)
            ->assertJson(['error' => 'Можно добавить не более 20 напитков.']);
    }

    public function test_user_must_be_authenticated()
    {
        $product = Product::factory()->create(['type' => 'pizza']);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])
            ->assertStatus(401);
    }

    public function test_validation_error_when_missing_fields()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/add', [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['product_id', 'quantity']);
    }
}
