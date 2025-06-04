<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AdminOrderControllerTest extends TestCase
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

    public function test_orders_returns_paginated_orders()
    {
        $this->authenticateAdmin();

        Order::factory()->count(5)->has(
            OrderItem::factory()->count(2)->for(
                Product::factory()
            )
        )->create();

        $response = $this->getJson('/api/admin/orders?per_page=3');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'order_items' => [
                            '*' => [
                                'id',
                                'product' => [
                                    'id',
                                    'name',
                                ]
                            ]
                        ]
                    ]
                ],
                'links',
            ]);
    }

    public function test_orderDetails_returns_single_order_with_items()
    {
        $this->authenticateAdmin();

        $order = Order::factory()
            ->has(OrderItem::factory()->count(2)->for(Product::factory()))
            ->create();

        $response = $this->getJson("/api/admin/orders/{$order->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'id',
                'order_items' => [
                    '*' => [
                        'id',
                        'product' => [
                            'id',
                            'name',
                        ]
                    ]
                ]
            ]);
    }
}
