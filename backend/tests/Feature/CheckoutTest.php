<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_place_order()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create([
            'sale_price' => 100,
            'stock_qty' => 10
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/storefront/checkout', [
            'email' => 'customer@example.com',
            'first_name' => 'John',
            'last_name' => 'Doe',
            'address' => '123 Main St',
            'city' => 'New York',
            'postal_code' => '10001',
            'payment_method' => 'cod',
            'items' => [
                [
                    'id' => $product->id,
                    'quantity' => 2
                ]
            ]
        ]);

        $response->assertStatus(201)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('orders', [
            'total_amount' => 215.00, // (100 * 2) + 15 shipping
        ]);

        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $this->assertEquals(8, $product->fresh()->stock_qty);
    }
}
