<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Product;
use App\Models\User;
use App\Models\Customer;
use App\Models\LandingPage;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Disable foreign key checks and truncate tables for a fresh start
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Unit::truncate();
        \App\Models\Brand::truncate();
        \App\Models\Category::truncate();
        \App\Models\Product::truncate();
        \App\Models\Customer::truncate();
        \App\Models\LandingPage::truncate();
        \Illuminate\Support\Facades\DB::table('category_product')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $vendors = User::role('vendor')->get();

        if ($vendors->isEmpty()) {
            $this->command->warn('No vendors found. Please run RoleSeeder first.');
            return;
        }

        foreach ($vendors as $vendor) {
            $this->command->info("Seeding data for Vendor: {$vendor->name} ({$vendor->email})");

            // 1. Seed Units
            $units = [];
            $unitNames = [
                ['name' => 'Piece', 'symbol' => 'pc'],
                ['name' => 'Kilogram', 'symbol' => 'kg'],
                ['name' => 'Box', 'symbol' => 'bx'],
                ['name' => 'Meter', 'symbol' => 'm'],
                ['name' => 'Liter', 'symbol' => 'l'],
            ];
            foreach ($unitNames as $u) {
                $units[] = Unit::create([
                    'vendor_id' => $vendor->id,
                    'name'      => $u['name'],
                    'slug'      => Str::slug($u['name'] . '-' . $vendor->id . '-' . uniqid()),
                    'symbol'    => $u['symbol'],
                    'is_active' => true,
                ]);
            }

            // 2. Seed Brands
            $brands = [];
            $brandNames = ['Nike', 'Samsung', 'Apple', 'Sony', 'Adidas', 'L\'Oreal', 'Nestle'];
            foreach ($brandNames as $bName) {
                $brands[] = Brand::create([
                    'vendor_id'   => $vendor->id,
                    'name'        => $bName,
                    'slug'        => Str::slug($bName . '-' . $vendor->id . '-' . uniqid()),
                    'description' => "Official products from {$bName}",
                    'is_active'   => true,
                ]);
            }

            // 3. Seed Categories
            $categories = [];
            $catNames = [
                'Electronics' => ['Mobile Phones', 'Laptops', 'Accessories'],
                'Fashion'     => ['Men\'s Wear', 'Women\'s Wear', 'Shoes'],
                'Home Decor'  => ['Furniture', 'Lighting', 'Kitchen'],
                'Beauty'      => ['Skincare', 'Makeup', 'Haircare'],
            ];

            foreach ($catNames as $parentName => $subCats) {
                $parent = Category::create([
                    'vendor_id' => $vendor->id,
                    'name'      => $parentName,
                    'slug'      => Str::slug($parentName . '-' . $vendor->id . '-' . uniqid()),
                    'is_active' => true,
                ]);
                $categories[] = $parent;

                foreach ($subCats as $subName) {
                    $categories[] = Category::create([
                        'vendor_id' => $vendor->id,
                        'parent_id' => $parent->id,
                        'name'      => $subName,
                        'slug'      => Str::slug($subName . '-' . $vendor->id . '-' . uniqid()),
                        'is_active' => true,
                    ]);
                }
            }

            // 4. Seed Customers
            $faker = \Faker\Factory::create();
            for ($i = 1; $i <= 50; $i++) {
                Customer::create([
                    'vendor_id'  => $vendor->id,
                    'first_name' => $faker->firstName(),
                    'last_name'  => $faker->lastName(),
                    'email'      => "customer{$i}-{$vendor->id}@example.com",
                    'phone'      => "+8801" . rand(3, 9) . str_pad((string)rand(0, 99999999), 8, '0', STR_PAD_LEFT),
                    'password'   => bcrypt('password'),
                    'is_active'  => true,
                ]);
            }

            // 5. Seed Products
            $adjectives = ['Premium', 'Ultra', 'Minimalist', 'Smart', 'Professional', 'Eco-friendly', 'Ergonomic', 'Heavy-duty', 'Compact', 'Vintage', 'Modern', 'Sleek', 'Durable', 'Luxury', 'Classic'];
            $nouns = ['Wireless Headphones', 'Smartphone', 'Leather Wallet', 'Fitness Watch', 'DSLR Camera', 'Bluetooth Speaker', 'Mechanical Keyboard', 'Gaming Mouse', 'Backpack', 'Sunglasses', 'Desk Lamp', 'Water Bottle', 'Coffee Maker', 'Electric Toothbrush', 'Power Bank', 'Running Shoes', 'Yoga Mat', 'Drone', 'Monitor', 'Earbuds'];

            for ($idx = 0; $idx < 100; $idx++) {
                $randomWord = $faker->word();
                $productName = $faker->randomElement($adjectives) . ' ' . $faker->randomElement($nouns) . ' ' . ucfirst($randomWord) . ' ' . rand(10, 999);
                $productName = ucwords($productName);
                $price = $faker->randomFloat(2, 5, 2000);

                $product = Product::create([
                    'vendor_id'         => $vendor->id,
                    'brand_id'          => $brands[array_rand($brands)]->id,
                    'unit_id'           => $units[array_rand($units)]->id,
                    'name'              => $productName,
                    'slug'              => Str::slug($productName . '-' . $vendor->id . '-' . uniqid()),
                    'sku'               => strtoupper(Str::random(10)),
                    'product_code'      => 'PROD-V' . $vendor->id . '-' . str_pad((string)($idx + 1), 4, '0', STR_PAD_LEFT),
                    'short_description' => $faker->sentence(10),
                    'description'       => $faker->paragraphs(3, true),
                    'purchase_price'    => $price * 0.7,
                    'sale_price'        => $price,
                    'stock_qty'         => rand(10, 500),
                    'is_featured'       => $faker->boolean(15),
                    'is_trending'       => $faker->boolean(15),
                    'is_special'        => $faker->boolean(15),
                    'specifications'    => [
                        [
                            'title' => 'General Info',
                            'items' => [
                                ['label' => 'Material', 'value' => ucwords($faker->word())],
                                ['label' => 'Weight', 'value' => rand(100, 2000) . 'g'],
                                ['label' => 'Color', 'value' => ucwords($faker->colorName())],
                            ]
                        ],
                        [
                            'title' => 'Technical Specs',
                            'items' => [
                                ['label' => 'Warranty', 'value' => rand(1, 5) . ' Years'],
                                ['label' => 'Power', 'value' => rand(10, 100) . 'W'],
                            ]
                        ]
                    ],
                    'gallery'           => [],
                    'status'            => 'published',
                    'is_active'         => true,
                ]);

                // Attach to random 1-2 categories
                $randomCats = array_rand($categories, rand(1, 2));
                if (is_array($randomCats)) {
                    $product->categories()->attach([$categories[$randomCats[0]]->id, $categories[$randomCats[1]]->id]);
                } else {
                    $product->categories()->attach([$categories[$randomCats]->id]);
                }

                // 6. Create a Landing Page for some products (about 10% chance)
                if ($faker->boolean(10)) {
                    LandingPage::create([
                        'vendor_id'         => $vendor->id,
                        'product_id'        => $product->id,
                        'landing_page_type' => 'product',
                        'template_name'     => 'modern',
                        'title'             => "Special Deal: {$product->name}",
                        'slug'              => Str::slug("deal-{$product->name}-" . uniqid()),
                        'settings'          => [
                            'hero_title'    => "Get the {$product->name}",
                            'hero_subtitle' => "Limited time offer with extra discount.",
                            'primary_color' => $faker->hexcolor(),
                        ],
                        'status'            => 'active',
                    ]);
                }
            }
        }

        $this->command->info('✅ All shop data seeded successfully!');
    }
}
