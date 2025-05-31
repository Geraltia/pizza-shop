<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use Symfony\Component\HttpFoundation\Response;

class AdminControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function actingAsAdmin()
    {
        $admin = User::factory()->admin()->create();
        return $this->actingAs($admin, 'admin');
    }

    public function test_admin_can_list_products_with_pagination()
    {
        Product::factory()->count(15)->create();

        $response = $this->actingAsAdmin()->getJson('/api/admin/products?per_page=10');

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure(['data', 'meta']);
    }
}
