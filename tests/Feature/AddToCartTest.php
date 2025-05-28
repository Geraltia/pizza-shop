<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;

class AddToCartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_add_pizza_within_limit()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'pizza']);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart/add', [
                'product_id' => $product->id,
                'quantity' => 5,
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);
    }


    public function test_user_cannot_add_more_than_10_pizzas()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'pizza']);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 8,
        ]);

        $this->actingAs($user, 'sanctum');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Можно добавить не более 10 пицц.');

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }



    public function test_user_cannot_add_more_than_20_drinks()
    {
        $this->withoutExceptionHandling();

        $user = User::factory()->create();
        $product = Product::factory()->create(['type' => 'drink']);

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 19,
        ]);

        $this->actingAs($user, 'sanctum');

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Можно добавить не более 20 напитков.');

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }



    public function test_user_must_be_authenticated()
    {
        $product = Product::factory()->create(['type' => 'pizza']);

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(401);
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
