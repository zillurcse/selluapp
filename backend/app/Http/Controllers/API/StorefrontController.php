<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ShopSetting;
use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    private function resolveTenantId(Request $request)
    {
        // $domain = $request->header('X-Tenant-Domain') ?? $request->query('domain');

        $domain = 'vendor3.test';
        if (!$domain) {
            return null; // No specific tenant
        }

        // Check customDomain
        $customSetting = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->first();

        if ($customSetting) {
            return $customSetting->user_id;
        }

        // Check subDomain (Assuming main domain e.g. "demo.selluee.test" or "demo.localhost")
        $parts = explode('.', $domain);
        // If we have at least one dot, the first part might be a subdomain
        if (count($parts) >= 2) {
            $subdomain = $parts[0];
            $subSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'subDomain')
                ->where('value', $subdomain)
                ->first();

            if ($subSetting) {
                return $subSetting->user_id;
            }
        }

        return null;
    }

    public function index(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);
        
        // Check for Home Landing Page first
        $homeLandingPage = \App\Models\LandingPage::where('is_home', true)
            ->where('status', 'active')
            ->when($tenantId, fn($q) => $q->where('vendor_id', $tenantId))
            ->first();

        if ($homeLandingPage) {
            return $this->landingPage($homeLandingPage->slug);
        }

        $userId = $tenantId ?? 5; // fallback to 5 for default global sliders

        $isEssential = $request->query('only') === 'essential';
        $cacheKey = 'storefront_index_' . ($tenantId ?? 'global') . ($isEssential ? '_essential' : '_full');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($tenantId, $userId, $isEssential) {
            $query = ShopSetting::where('user_id', $userId);
            $query->where('group', 'website_sliders');
            $sliders = $query->get()->pluck('value', 'key')->map(function ($val, $key) {
                // Attempt to decode JSON if possible
                $decoded = json_decode($val, true);
                $parsedVal = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;

                // If the value is a file path starting with 'shop/', return the full URL
                if (is_string($parsedVal) && str_starts_with($parsedVal, 'shop/')) {
                    return Storage::disk('public')->url($parsedVal);
                }

                return $parsedVal;
            });

            $featuredProductsQuery = Product::with(['categories:id,name,slug', 'brand:id,name,slug', 'vendor.vendorProfile:id,user_id,store_name,store_slug'])
                ->select('id', 'name', 'sale_price', 'slug', 'image', 'thumbnail', 'vendor_id', 'brand_id', 'is_featured', 'is_active', 'status')
                ->where('is_featured', true)
                ->where('is_active', true)
                ->where('status', 'published')
                ->latest()
                ->take(8);

            if ($tenantId) {
                $featuredProductsQuery->where('vendor_id', $tenantId);
            }

            $featuredProducts = $featuredProductsQuery->get();

            $trendingProductsQuery = Product::with(['categories:id,name,slug', 'brand:id,name,slug'])
                ->select('id', 'name', 'sale_price', 'slug', 'image', 'thumbnail', 'vendor_id', 'brand_id', 'is_active', 'status', 'is_trending')
                ->where('is_trending', true)
                ->where('is_active', true)
                ->where('status', 'published')
                ->latest()
                ->take(8);

            if ($tenantId) {
                $trendingProductsQuery->where('vendor_id', $tenantId);
            }

            $trendingProducts = $trendingProductsQuery->get();

            $categoriesQuery = Category::with('children')
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->latest()
                ->take(12);

            if ($tenantId) {
                // Get categories that have products from this vendor
                $categoryIds = \Illuminate\Support\Facades\DB::table('category_product')
                    ->join('products', 'category_product.product_id', '=', 'products.id')
                    ->where('products.vendor_id', $tenantId)
                    ->where('products.is_active', true)
                    ->where('products.status', 'published')
                    ->distinct()
                    ->pluck('category_id');

                $categoriesQuery->whereIn('id', $categoryIds);
            }

            $categories = $categoriesQuery->get();

            // Vendor Profile info if tenant
            $vendorProfile = null;
            if ($tenantId) {
                $profile = \App\Models\VendorProfile::where('user_id', $tenantId)
                    ->select('id', 'user_id', 'store_name', 'store_slug', 'logo', 'banner')
                    ->first();
                if ($profile) {
                    $profile->logo_url = $profile->logo;
                    $profile->banner_url = $profile->banner;
                    $vendorProfile = $profile;
                }
            }

            if ($isEssential) {
                return response()->json([
                    'sliders' => $sliders,
                    'categories' => $categories,
                    'vendor' => $vendorProfile,
                    'loyalty_program' => ShopSetting::where('user_id', $userId)->where('group', 'loyalty_program')->get()->pluck('value', 'key'),
                    'marketing' => ShopSetting::where('user_id', $userId)->where('group', 'marketing_social')->get()->pluck('value', 'key')
                ]);
            }

            $brands = \App\Models\Brand::where('is_active', true)->latest()->get();
            $units = \App\Models\Unit::where('is_active', true)->get();

            // format products
            $categoryWiseProductsQuery = Category::where('is_active', true)
                ->whereNull('parent_id')
                ->whereHas('products', function($q) use ($tenantId) {
                    $q->where('is_active', true)
                      ->where('status', 'published');
                    if ($tenantId) {
                        $q->where('vendor_id', $tenantId);
                    }
                })
                ->with(['products' => function($q) use ($tenantId) {
                    $q->where('is_active', true)
                      ->where('status', 'published')
                      ->select('products.id', 'products.name', 'products.sale_price', 'products.slug', 'products.image', 'products.thumbnail', 'products.vendor_id', 'products.brand_id', 'products.is_active', 'products.status')
                      ->with(['categories:id,name,slug', 'brand:id,name,slug']);
                    if ($tenantId) {
                        $q->where('vendor_id', $tenantId);
                    }
                    $q->orderBy('products.created_at', 'desc')->take(8);
                }])
                ->latest()
                ->take(3);

            $categoryWiseProducts = $categoryWiseProductsQuery->get();

            // format products
            $featuredProducts = \App\Http\Resources\Storefront\ProductResource::collection($featuredProducts)->resolve();
            $trendingProducts = \App\Http\Resources\Storefront\ProductResource::collection($trendingProducts)->resolve();

            // format products for each category
            $categoryWiseProducts = $categoryWiseProducts->map(function ($category) {
                $cat = $category->toArray();
                $cat['products'] = \App\Http\Resources\Storefront\ProductResource::collection($category->products)->resolve();
                return $cat;
            });

            // Fetch active promotions
            $promotionsQuery = \App\Models\Promotion::where('is_active', true)
                ->where('start_date', '<=', now())
                ->where('end_date', '>=', now())
                ->orderBy('priority', 'desc');

            if ($tenantId) {
                $promotionsQuery->where('vendor_id', $tenantId);
            }

            $promotions = $promotionsQuery->get()->map(function($promo) {
                if ($promo->banner) {
                    $promo->banner_url = \Illuminate\Support\Str::contains($promo->banner, ['http://', 'https://']) 
                        ? $promo->banner 
                        : Storage::disk('public')->url($promo->banner);
                } else {
                    $promo->banner_url = null;
                }
                $promo->slug = $promo->slug ?: \Illuminate\Support\Str::slug($promo->title); // Fallback if not yet migrated
                
                // Hydrate rules/targets with slugs for easier frontend linking
                $rules = is_string($promo->rules) ? json_decode($promo->rules, true) : $promo->rules;
                
                if ($promo->type === 'buy_x_get_y' && isset($rules['buy_product_id'])) {
                    $p = \App\Models\Product::find($rules['buy_product_id']);
                    if ($p) $rules['buy_product_slug'] = $p->slug;
                } elseif ($promo->type === 'bundle' && isset($rules['required_items'])) {
                    $slugs = \App\Models\Product::whereIn('id', $rules['required_items'])->pluck('slug')->toArray();
                    $rules['product_slugs'] = $slugs;
                } elseif ($promo->type === 'category' && !empty($promo->target_ids)) {
                    $slugs = \App\Models\Category::whereIn('id', $promo->target_ids)->pluck('slug')->toArray();
                    $promo->category_slugs = $slugs;
                }

                $promo->rules = $rules;
                return $promo;
            });

            // Fetch website banners
            $websiteBanners = ShopSetting::where('user_id', $userId)
                ->where('group', 'website_banners')
                ->get()
                ->pluck('value', 'key')
                ->map(function ($val, $key) {
                    $decoded = json_decode($val, true);
                    $parsedVal = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;

                    if (is_string($parsedVal) && str_starts_with($parsedVal, 'shop/')) {
                        return Storage::disk('public')->url($parsedVal);
                    }

                    return $parsedVal;
                });

            // Fetch marketing/social settings
            $marketingSettings = ShopSetting::where('user_id', $userId)
                ->where('group', 'marketing_social')
                ->get()
                ->pluck('value', 'key');

            // Fetch custom pages
            $customPagesSettings = ShopSetting::where('user_id', $userId)
                ->where('group', 'custom_pages')
                ->first();

            $customPages = [];
            if ($customPagesSettings && isset($customPagesSettings->value)) {
                $decoded = json_decode($customPagesSettings->value, true);
                if (json_last_error() === JSON_ERROR_NONE && isset($decoded['pages'])) {
                    $customPages = $decoded['pages'];
                }
            }

            return response()->json([
                'sliders' => $sliders,
                'website_banners' => $websiteBanners,
                'featured_products' => $featuredProducts,
                'trending_products' => $trendingProducts,
                'category_wise_products' => $categoryWiseProducts,
                'categories' => $categories,
                'brands' => $brands,
                'units' => $units,
                'vendor' => $vendorProfile,
                'promotions' => $promotions,
                'marketing' => $marketingSettings,
                'custom_pages' => reset($customPages) === false && count($customPages) > 0 && isset($customPages[0]) ? $customPages : array_values($customPages),
                'loyalty_program' => ShopSetting::where('user_id', $userId)->where('group', 'loyalty_program')->get()->pluck('value', 'key')
            ]);
        });
    }

    public function products(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);

        $query = Product::with(['categories', 'brand', 'unit', 'vendor.vendorProfile'])
            ->where('is_active', true)
            ->where('status', 'published');

        if ($tenantId) {
            $query->where('vendor_id', $tenantId);
        }


        if ($request->filled('category') && $request->category != 'all') {
            $allCategorySlugs = Category::getDescendantsSlugs($request->category);
            $query->whereHas('categories', function($q) use ($allCategorySlugs) {
                $q->whereIn('slug', $allCategorySlugs);
            });
        }

        if ($request->filled('categories')) {
            $categories = is_array($request->categories) ? $request->categories : explode(',', $request->categories);
            $categories = array_filter($categories);
            if (!empty($categories)) {
                $allCategorySlugs = Category::getDescendantsSlugs($categories);
                $query->whereHas('categories', function($q) use ($allCategorySlugs) {
                    $q->whereIn('slug', $allCategorySlugs);
                });
            }
        }

        if ($request->filled('brands')) {
            $brands = is_array($request->brands) ? $request->brands : explode(',', $request->brands);
            $brands = array_filter($brands);
            if (!empty($brands)) {
                $query->whereHas('brand', function($q) use ($brands) {
                    $q->whereIn('slug', $brands);
                });
            }
        }

        if ($request->filled('units')) {
            $units = is_array($request->units) ? $request->units : explode(',', $request->units);
            $units = array_filter($units);
            if (!empty($units)) {
                $query->whereHas('unit', function($q) use ($units) {
                    $q->whereIn('slug', $units);
                });
            }
        }

        // Filtering by specifications (JSON)
        if ($request->filled('specs')) {
            $specs = $request->specs;
            if (is_string($specs)) {
                $specs = json_decode($specs, true);
            }

            if (is_array($specs)) {
                foreach ($specs as $key => $values) {
                    $values = is_array($values) ? $values : [$values];
                    $values = array_filter($values);

                    if (!empty($values)) {
                        $query->where(function($q) use ($key, $values) {
                            foreach ($values as $value) {
                                $q->orWhere(function($subq) use ($key, $value) {
                                    // Search both structured and flat formats
                                    $subq->where('specifications', 'like', '%' . $key . '%')
                                         ->where('specifications', 'like', '%' . $value . '%');
                                });
                            }
                        });
                    }
                }
            }
        }

        if ($request->filled('min_price')) {
            $query->where('sale_price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('sale_price', '<=', $request->max_price);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('is_trending') && filter_var($request->is_trending, FILTER_VALIDATE_BOOLEAN)) {
            $query->where('is_trending', true);
        }

        // Filter by Promotion
        if ($request->filled('promotion_id') || $request->filled('promotion')) {
            $promotion = null;
            if ($request->filled('promotion_id')) {
                $promotion = \App\Models\Promotion::find($request->promotion_id);
            } elseif ($request->filled('promotion')) {
                $promotion = \App\Models\Promotion::where('slug', $request->promotion)->first() 
                            ?? \App\Models\Promotion::where('title', $request->promotion)->first();
            }

            if ($promotion) {
                if ($promotion->type === 'buy_x_get_y') {
                    $rules = is_string($promotion->rules) ? json_decode($promotion->rules, true) : $promotion->rules;
                    $ids = array_filter([$rules['buy_product_id'] ?? null, $rules['get_product_id'] ?? null]);
                    if (!empty($ids)) $query->whereIn('id', $ids);
                } elseif ($promotion->type === 'bundle') {
                    $rules = is_string($promotion->rules) ? json_decode($promotion->rules, true) : $promotion->rules;
                    if (!empty($rules['required_items'])) {
                        $query->whereIn('id', $rules['required_items']);
                    }
                } elseif ($promotion->type === 'category') {
                    if (!empty($promotion->target_ids)) {
                        $query->whereHas('categories', function($q) use ($promotion) {
                            $q->whereIn('id', $promotion->target_ids);
                        });
                    }
                } else {
                    if ($promotion->target === 'selected' && !empty($promotion->target_ids)) {
                        $query->whereIn('id', $promotion->target_ids);
                    } elseif ($promotion->target === 'categories' && !empty($promotion->target_ids)) {
                        $query->whereHas('categories', function($q) use ($promotion) {
                            $q->whereIn('id', $promotion->target_ids);
                        });
                    }
                }
            }
        }

        if ($request->filled('sort')) {
            switch ($request->sort) {
                case 'price_asc':
                    $query->orderBy('sale_price', 'asc');
                    break;
                case 'price_desc':
                    $query->orderBy('sale_price', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'featured':
                    $query->orderBy('is_featured', 'desc');
                    break;
                default:
                    $query->latest();
                    break;
            }
        } else {
            $query->latest();
        }

        if ($request->has('offset')) {
            $limit = $request->get('limit', 10);
            $offset = $request->get('offset', 0);
            
            $total = (clone $query)->count();
            $items = $query->offset($offset)->limit($limit)->get();
            
            $items = \App\Http\Resources\Storefront\ProductResource::collection($items)->resolve();
            
            return response()->json([
                'data' => $items,
                'total' => $total,
                'per_page' => $limit,
                'current_page' => floor($offset / $limit) + 1,
                'last_page' => ceil($total / $limit)
            ]);
        }

        $perPage = $request->get('per_page', 10);
        $products = $query->paginate($perPage);
        $products->getCollection()->transform(function ($product) {
            return (new \App\Http\Resources\Storefront\ProductResource($product))->resolve();
        });

        return response()->json($products);
    }

    public function categories()
    {
        return response()->json(Category::where('is_active', true)->whereNull('parent_id')->get());
    }

    public function infiniteCategories(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);
        
        $perPage = $request->get('limit', 1); // Load 1 category per request
        
        $query = Category::where('is_active', true)
            ->whereNull('parent_id')
            ->whereHas('products', function($q) use ($tenantId) {
                $q->where('is_active', true)
                  ->where('status', 'published');
                if ($tenantId) {
                    $q->where('vendor_id', $tenantId);
                }
            })
            ->with(['products' => function($q) use ($tenantId) {
                $q->where('is_active', true)
                  ->where('status', 'published')
                  ->with(['categories', 'brand']);
                if ($tenantId) {
                    $q->where('vendor_id', $tenantId);
                }
                $q->orderBy('products.created_at', 'desc')->take(12);
            }])
            ->latest();

        $categories = $query->paginate($perPage);

        // format products for each category
        $categories->getCollection()->transform(function ($category) {
            $cat = $category->toArray();
            $cat['products'] = \App\Http\Resources\Storefront\ProductResource::collection($category->products)->resolve();
            return $cat;
        });

        return response()->json($categories);
    }

    public function brands()
    {
        return response()->json(\App\Models\Brand::where('is_active', true)->get());
    }

    public function units()
    {
        return response()->json(\App\Models\Unit::where('is_active', true)->get());
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)
            ->where('is_active', true)
            ->where('status', 'published')
            ->firstOrFail();
        $product->load(['categories', 'brand', 'unit', 'vendor.vendorProfile', 'variants.attributes.attribute']);
        return response()->json(new \App\Http\Resources\Storefront\ProductResource($product));
    }

    public function vendor($slug)
    {
        $vendorProfile = \App\Models\VendorProfile::where('store_slug', $slug)->firstOrFail();
        $user = $vendorProfile->user;

        $products = Product::with(['categories', 'brand', 'unit', 'vendor.vendorProfile'])
            ->where('vendor_id', $user->id)
            ->where('is_active', true)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);
        $products->getCollection()->transform(function ($product) {
            return (new \App\Http\Resources\Storefront\ProductResource($product))->resolve();
        });

        // Get categories that have products from this vendor
        $categoryIds = \Illuminate\Support\Facades\DB::table('category_product')
            ->join('products', 'category_product.product_id', '=', 'products.id')
            ->where('products.vendor_id', $user->id)
            ->where('products.is_active', true)
            ->where('products.status', 'published')
            ->distinct()
            ->pluck('category_id');

        $categories = Category::whereIn('id', $categoryIds)->whereNull('parent_id')->get();

        // Get sliders for this vendor
        $sliders = ShopSetting::where('user_id', $user->id)
            ->where('group', 'website_sliders')
            ->get()
            ->pluck('value', 'key')
            ->map(function ($val, $key) {
                $decoded = json_decode($val, true);
                $parsedVal = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
                if (is_string($parsedVal) && str_starts_with($parsedVal, 'shop/')) {
                    return Storage::disk('public')->url($parsedVal);
                }
                return $parsedVal;
            });

        // Format vendor profile images
        $vendorProfile->logo_url = $vendorProfile->logo ? Storage::disk('public')->url($vendorProfile->logo) : null;
        $vendorProfile->banner_url = $vendorProfile->banner ? Storage::disk('public')->url($vendorProfile->banner) : null;

        return response()->json([
            'vendor' => $vendorProfile,
            'products' => $products,
            'categories' => $categories,
            'sliders' => $sliders,
            'loyalty_program' => ShopSetting::where('user_id', $user->id)->where('group', 'loyalty_program')->get()->pluck('value', 'key')
        ]);
    }

    public function landingPage($slug)
    {
        $landingPage = \App\Models\LandingPage::where('slug', $slug)
            ->where('status', 'active')
            ->firstOrFail();

        $products = [];

        if ($landingPage->landing_page_type === 'common') {
            // Common type: no specific products, return empty or featured products from vendor
            $vendorProducts = Product::with(['categories', 'brand', 'unit', 'vendor.vendorProfile'])
                ->where('vendor_id', $landingPage->vendor_id)
                ->where('is_active', true)
                ->where('status', 'published')
                ->latest()
                ->take(8)
                ->get();
            $products = \App\Http\Resources\Storefront\ProductResource::collection($vendorProducts)->resolve();
        } else {
            // Single or Multiple: resolve product IDs in order
            if ($landingPage->landing_page_type === 'multiple') {
                $productIds = $landingPage->settings['product_ids'] ?? [$landingPage->product_id];
            } else {
                $productIds = [$landingPage->product_id];
            }

            $productIds = array_filter($productIds);

            if (!empty($productIds)) {
                // Preserve admin-set order using FIELD() or collect sort
                $productMap = Product::with(['categories', 'brand', 'unit', 'vendor.vendorProfile'])
                    ->whereIn('id', $productIds)
                    ->where('is_active', true)
                    ->where('status', 'published')
                    ->get()
                    ->keyBy('id');

                $ordered = collect($productIds)
                    ->filter(fn($id) => $productMap->has($id))
                    ->map(fn($id) => $productMap[$id])
                    ->values();

                $products = \App\Http\Resources\Storefront\ProductResource::collection($ordered)->resolve();
            }
        }

        $vendorProfile = \App\Models\VendorProfile::where('user_id', $landingPage->vendor_id)->first();
        if ($vendorProfile) {
            $vendorProfile->logo_url = $vendorProfile->logo ? Storage::disk('public')->url($vendorProfile->logo) : null;
            $vendorProfile->banner_url = $vendorProfile->banner ? Storage::disk('public')->url($vendorProfile->banner) : null;
        }

        return response()->json([
            'landing_page' => $landingPage,
            'products'     => $products,
            'vendor'       => $vendorProfile,
            'loyalty_program' => ShopSetting::where('user_id', $landingPage->vendor_id)->where('group', 'loyalty_program')->get()->pluck('value', 'key')
        ]);
    }

    public function states(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);
        $states = \App\Models\State::where('status', true);
        if ($tenantId) {
            // If you want to limit states per vendor, you'd add logic here.
            // For now, we return all active states.
        }
        return response()->json(['success' => true, 'data' => $states->get()]);
    }

    public function cities(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);
        $query = \App\Models\City::where('status', true);
        
        if ($request->filled('state_id')) {
            $query->where('state_id', $request->state_id);
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        $cities = $query->get();
        return response()->json(['success' => true, 'data' => $cities]);
    }



    public function estimateShipping(Request $request)
    {
        $city_id = $request->city_id;
        $userId = Auth::check() ? Auth::id() : null;
        $tempUserId = Auth::check() ? null : ($request->header('X-Temp-User-Id') ?? session('temp_user_id'));

        \Log::info("estimateShipping called. CityID: {$city_id}, Items count: " . (is_array($request->items) ? count($request->items) : 0));

        // If items are passed directly (for guest checkout or unsynced cart), use them
        if ($request->has('items') && is_array($request->items)) {
            $rawItems = $request->items;
            $carts = collect();
            foreach ($rawItems as $item) {
                $carts->push([
                    'product_id' => $item['id'] ?? $item['product_id'],
                    'quantity' => $item['quantity'],
                    'id' => $item['id'] ?? 0
                ]);
            }
        } else {
            $carts = Cart::where(function ($query) use ($userId, $tempUserId) {
                if ($userId) {
                    $query->where('user_id', $userId);
                } elseif ($tempUserId) {
                    $query->where('temp_user_id', $tempUserId);
                } else {
                    $query->where('temp_user_id', '___NON_EXISTENT___'); // Avoid matching nulls
                }
            })->active()->get();
        }

        \Log::info("Carts to process: " . ($carts ? $carts->count() : 0));

        if (!$carts || $carts->isEmpty()) {
            return response()->json(['status' => 0, 'cost' => 0, 'message' => 'Cart empty']);
        }

        $shipping_info = ['country_id' => 1, 'city_id' => $city_id];
        $total_shipping = 0;

        foreach ($carts as $key => $cartItem) {
            $cost = getShippingCost($carts, $key, $shipping_info);
            $total_shipping += $cost;
            // Only update DB if it's a real model
            if ($cartItem instanceof \Illuminate\Database\Eloquent\Model) {
                $cartItem['shipping_cost'] = $cost;
                $cartItem->save();
            }
        }

        \Log::info("Total calculation result: $total_shipping");

        // Determine if any vendor in the cart uses carrier_wise or other special methods
        $methods = [];
        $carriers = [];
        foreach ($carts as $item) {
            $item_p = \App\Models\Product::find($item['product_id']);
            if ($item_p) {
                $vId = $item_p->vendor_id;
                $m = get_vendor_business_setting($vId, 'shipping_method', 'flat_rate');
                $methods[] = $m;
                if ($m === 'carrier_wise') {
                    $c_prices = get_vendor_business_setting($vId, 'carrier_prices', []);
                    if (is_array($c_prices)) {
                        foreach ($c_prices as $c_key => $c_price) {
                            if ($c_price > 0) {
                                $carriers[$c_key] = ucfirst($c_key);
                            }
                        }
                    }
                }
            }
        }

        $shipping_method = count(array_unique($methods)) === 1 ? $methods[0] : 'mixed';

        $available_carriers = [];
        foreach ($carriers as $id => $name) {
            $available_carriers[] = ['id' => $id, 'name' => $name];
        }

        return response()->json([
            'status'    => 1,
            'cost'      => (float)$total_shipping,
            'formatted' => format_price(convert_price($total_shipping)),
            'method'    => $shipping_method,
            'available_carriers' => $available_carriers
        ]);
    }

    public function reviews(Request $request, \App\Models\Product $product)
    {
        $tenantId = $this->resolveTenantId($request);
        
        // Ensure product belongs to tenant if applicable
        if ($tenantId && $product->vendor_id != $tenantId) {
            return response()->json(['message' => 'Product not found.'], 404);
        }

        $reviews = $product->reviews()
                           ->with('customer:id,first_name,last_name')
                           ->where('status', 'approved')
                           ->latest()
                           ->paginate($request->get('per_page', 10));

        // Format customer names to just first name + initial
        $reviews->getCollection()->transform(function($review) {
            $customerName = 'Anonymous Customer';
            if ($review->customer) {
                $firstName = $review->customer->first_name;
                $lastNameInitial = $review->customer->last_name ? substr($review->customer->last_name, 0, 1) . '.' : '';
                $customerName = trim($firstName . ' ' . $lastNameInitial);
            }
            return [
                'id' => $review->id,
                'rating' => $review->rating,
                'comment' => $review->comment,
                'is_verified' => $review->is_verified,
                'customer_name' => $customerName,
                'created_at' => $review->created_at->diffForHumans(),
                'date' => $review->created_at->format('M d, Y')
            ];
        });

        $stats = [
            'total_reviews' => $product->reviews()->where('status', 'approved')->count(),
            'average_rating' => (float) $product->reviews()->where('status', 'approved')->avg('rating'),
            'rating_counts' => [
                5 => $product->reviews()->where('status', 'approved')->where('rating', 5)->count(),
                4 => $product->reviews()->where('status', 'approved')->where('rating', 4)->count(),
                3 => $product->reviews()->where('status', 'approved')->where('rating', 3)->count(),
                2 => $product->reviews()->where('status', 'approved')->where('rating', 2)->count(),
                1 => $product->reviews()->where('status', 'approved')->where('rating', 1)->count(),
            ]
        ];

        return response()->json([
            'reviews' => $reviews,
            'stats' => $stats
        ]);
    }
}

