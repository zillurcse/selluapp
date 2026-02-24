<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class StorefrontTest extends TestCase
{
    use RefreshDatabase;

    public function test_storefront_index_returns_success()
    {
        $response = $this->getJson('/api/storefront');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'sliders',
                'featured_products',
                'trending_products',
                'category_wise_products',
                'categories',
                'brands',
                'units',
                'vendor'
            ]);
    }

    public function test_storefront_products_returns_paginated_data()
    {
        \App\Models\Product::factory()->count(5)->create([
            'is_active' => true,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/storefront/products');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'total'
            ]);
    }

    public function test_storefront_product_show_returns_product_details()
    {
        $product = \App\Models\Product::factory()->create([
            'is_active' => true,
            'status' => 'published',
        ]);

        $response = $this->getJson('/api/storefront/products/' . $product->slug);

        $response->assertStatus(200)
            ->assertJsonPath('id', $product->id)
            ->assertJsonPath('slug', $product->slug);
    }
}
