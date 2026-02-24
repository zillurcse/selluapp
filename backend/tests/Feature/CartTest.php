<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_cart()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();
        \App\Models\Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 2
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/cart');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.quantity', 2);
    }

    public function test_user_can_add_to_cart()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson('/api/cart', [
                'product_id' => $product->id,
                'quantity' => 1
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Added to cart']);

        $this->assertDatabaseHas('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);
    }

    public function test_user_can_update_cart_quantity()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();
        $cart = \App\Models\Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->putJson('/api/cart/' . $product->id, [
                'quantity' => 5
            ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Cart updated']);

        $this->assertDatabaseHas('carts', [
            'id' => $cart->id,
            'quantity' => 5
        ]);
    }

    public function test_user_can_remove_from_cart()
    {
        $user = \App\Models\User::factory()->create();
        $product = \App\Models\Product::factory()->create();
        \App\Models\Cart::create([
            'user_id' => $user->id,
            'product_id' => $product->id,
            'quantity' => 1
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson('/api/cart/' . $product->id);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Removed from cart']);

        $this->assertDatabaseMissing('carts', [
            'user_id' => $user->id,
            'product_id' => $product->id
        ]);
    }
}
