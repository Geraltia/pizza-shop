<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guest_cannot_create_order()
    {
        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(401);
    }

    /** @test */
    public function cannot_create_order_with_empty_cart()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'delivery_time' => now()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'Корзина пуста']);
    }

    /** @test */
    public function cannot_create_order_with_invalid_data()
    {
        $user = User::factory()->create();


        $response = $this->actingAs($user)->postJson('/api/orders', []);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['phone', 'email', 'address', 'delivery_time']);
    }

    /** @test */
    public function can_create_order_with_valid_data_and_cart()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->postJson('/api/orders', [
            'phone' => '1234567890',
            'email' => 'test@example.com',
            'address' => 'Test Address',
            'delivery_time' => now()->addHour()->format('Y-m-d H:i:s'),
        ]);

        $response->assertCreated();
        $response->assertJsonStructure([
            'message',
            'order_id',
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $user->id,
            'phone' => '1234567890',
        ]);
    }

    /** @test */
    public function guest_cannot_view_orders()
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    /** @test */
    public function user_can_view_their_orders()
    {
        $user = User::factory()->create();

        $orders = Order::factory()->count(3)->create([
            'user_id' => $user->id,
        ]);

        Order::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/orders');

        $response->assertOk()
            ->assertJsonCount(3, 'orders')
            ->assertJsonStructure([
                'orders' => [
                    '*' => ['id', 'phone', 'email', 'address', 'delivery_time', 'created_at', 'updated_at'],
                ]
            ]);
    }

    /** @test */
    public function user_with_no_orders_gets_empty_list()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->getJson('/api/orders');

        $response->assertOk()
            ->assertJson([
                'orders' => [],
            ]);
    }

}
