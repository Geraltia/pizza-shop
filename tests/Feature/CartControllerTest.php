<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\CartItem;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;

class CartControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    use RefreshDatabase;

    protected CartService $cartServiceMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cartServiceMock = Mockery::mock(CartService::class);
        $this->app->instance(CartService::class, $this->cartServiceMock);
    }

    public function test_addToCart_calls_service_and_returns_cart_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cartItem = CartItem::factory()->make([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,

        ]);

        $this->actingAs($user, 'sanctum');

        $this->cartServiceMock
            ->shouldReceive('addToCart')
            ->once()
            ->with($product->id, 3)
            ->andReturn($cartItem);

        $response = $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 3,
            'type' => $product->type->value
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 3,
        ]);
    }

    public function test_addToCart_requires_authentication()
    {
        $product = Product::factory()->create();

        $this->postJson('/api/cart/add', [
            'product_id' => $product->id,
            'quantity' => 1,
        ])->assertStatus(401);
    }

    public function test_decreaseFromCart_decreases_quantity_and_returns_item()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 5,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/cart/decrease', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->assertDatabaseHas('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
    }

    public function test_decreaseFromCart_removes_item_if_quantity_zero_or_less()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->postJson('/api/cart/decrease', [
            'product_id' => $product->id,
            'quantity' => 3,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Товар удалён из корзины.',
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_removeFromCart_deletes_cart_item_and_returns_message()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson('/api/cart/remove', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Товар удалён из корзины.',
        ]);

        $this->assertDatabaseMissing('cart_items', [
            'user_id' => $user->id,
            'product_id' => $product->id,
        ]);
    }

    public function test_removeFromCart_returns_message_even_if_item_not_found()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->deleteJson('/api/cart/remove', [
            'product_id' => $product->id,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Товар удалён из корзины.',
        ]);
    }

    public function test_viewCart_returns_cart_items_with_products()
    {
        $user = User::factory()->create();
        $product = Product::factory()->create();

        $cartItem = CartItem::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 4,
        ]);

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'id' => $cartItem->id,
            'quantity' => 4,
            'product_id' => $product->id,
        ]);

        $response->assertJsonStructure([
            '*' => [
                'id',
                'user_id',
                'product_id',
                'quantity',
                'product' => [
                    'id',
                    'type',
                    'name',
                    // и другие поля продукта
                ],
            ],
        ]);
    }

    public function test_viewCart_returns_empty_array_if_no_items()
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/cart');

        $response->assertStatus(200);
        $response->assertExactJson([]);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }
}
