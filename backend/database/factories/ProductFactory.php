<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vendor_id' => \App\Models\User::factory(),
            'name' => $this->faker->words(3, true),
            'slug' => $this->faker->unique()->slug,
            'sale_price' => $this->faker->randomFloat(2, 10, 1000),
            'stock_qty' => $this->faker->numberBetween(0, 100),
            'is_featured' => $this->faker->boolean,
            'is_trending' => $this->faker->boolean,
            'is_active' => true,
            'status' => 'published',
        ];
    }
}
