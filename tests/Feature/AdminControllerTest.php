<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function authenticateAdmin()
    {
        $admin = User::factory()->create(['is_admin' => true]);
        Sanctum::actingAs($admin);
        return $admin;
    }

    public function test_admin_can_add_product()
    {
        $this->authenticateAdmin();

        $data = [
            'name' => 'Test Product',
            'price' => 99.99,
            'description' => 'Test description',
        ];

        $response = $this->postJson('/api/admin/products', $data);

        $response->assertStatus(Response::HTTP_CREATED)
            ->assertJsonStructure(['product' => ['id', 'name', 'price', 'description']]);

        $this->assertDatabaseHas('products', ['name' => 'Test Product']);
    }

    public function test_admin_can_delete_product()
    {
        $this->authenticateAdmin();

        $product = Product::factory()->create();

        $response = $this->deleteJson("/api/admin/products/{$product->id}");

        $response->assertStatus(Response::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_admin_can_list_orders_with_pagination()
    {
        $this->authenticateAdmin();

        Order::factory()->count(5)->create();

        $response = $this->getJson('/api/admin/orders?per_page=3');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data']);
    }

    public function test_admin_can_view_order_details()
    {
        $this->authenticateAdmin();

        $order = Order::factory()->create();
        $product = Product::factory()->create();

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $product->id,
        ]);

        $response = $this->getJson("/api/admin/orders/{$order->id}");

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonFragment(['id' => $order->id]);
    }

    protected function authenticateUser()
    {
        $user = User::factory()->create(['is_admin' => false]);
        Sanctum::actingAs($user);
    }

    public function test_admin_can_list_products_with_pagination()
    {
        $this->authenticateAdmin();

        Product::factory()->count(15)->create();

        $response = $this->getJson('/api/admin/products?per_page=10');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'data',
            ]);
    }

    public function test_non_admin_cannot_list_products()
    {
        $this->authenticateUser();

        $response = $this->getJson('/api/admin/products');

        $response->assertStatus(Response::HTTP_FORBIDDEN)
            ->assertJson(['message' => 'Доступ запрещён. Админ только.']);
    }

    public function test_guest_user_cannot_list_products()
    {
        $response = $this->getJson('/api/admin/products');

        $response->assertStatus(Response::HTTP_UNAUTHORIZED);
    }






}
