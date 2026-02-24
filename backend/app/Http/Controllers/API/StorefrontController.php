<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StorefrontController extends Controller
{
    private function resolveTenantId(Request $request)
    {
//        $domain = $request->header('X-Tenant-Domain') ?? $request->query('domain');
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

        // Check subDomain (Assuming main domain e.g. "demo.selluee.test")
        $parts = explode('.', $domain);
        if (count($parts) > 2) {
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
        $userId = $tenantId ?? 5; // fallback to 5 for default global sliders

        $cacheKey = 'storefront_index_' . ($tenantId ?? 'global');

        return \Illuminate\Support\Facades\Cache::remember($cacheKey, 600, function () use ($tenantId, $userId) {
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

            $featuredProductsQuery = Product::with(['categories', 'brand', 'vendor.vendorProfile'])
                ->where('is_featured', true)
                ->where('is_active', true)
                ->where('status', 'published')
                ->latest()
                ->take(8);

            if ($tenantId) {
                $featuredProductsQuery->where('vendor_id', $tenantId);
            }

            $featuredProducts = $featuredProductsQuery->get();

            $trendingProductsQuery = Product::with(['categories', 'brand'])
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
                $profile = \App\Models\VendorProfile::where('user_id', $tenantId)->first();
                if ($profile) {
                    $profile->logo_url = $profile->logo ? Storage::disk('public')->url($profile->logo) : null;
                    $profile->banner_url = $profile->banner ? Storage::disk('public')->url($profile->banner) : null;
                    $vendorProfile = $profile;
                }
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
                      ->with(['categories', 'brand']);
                    if ($tenantId) {
                        $q->where('vendor_id', $tenantId);
                    }
                    $q->latest()->take(8);
                }])
                ->latest()
                ->take(3);

            $categoryWiseProducts = $categoryWiseProductsQuery->get();

            // format products
            $this->formatProducts($featuredProducts);
            $this->formatProducts($trendingProducts);

            // format products for each category
            foreach ($categoryWiseProducts as $category) {
                $this->formatProducts($category->products);
            }

            return response()->json([
                'sliders' => $sliders,
                'featured_products' => $featuredProducts,
                'trending_products' => $trendingProducts,
                'category_wise_products' => $categoryWiseProducts,
                'categories' => $categories,
                'brands' => $brands,
                'units' => $units,
                'vendor' => $vendorProfile
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
            
            $this->formatProducts($items);
            
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

        $this->formatProducts($products->getCollection());

        return response()->json($products);
    }

    public function categories()
    {
        return response()->json(Category::where('is_active', true)->whereNull('parent_id')->get());
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
        $this->formatSingleProduct($product);
        return response()->json($product);
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

        $this->formatProducts($products->getCollection());

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
            'sliders' => $sliders
        ]);
    }

    private function formatProducts($products)
    {
        foreach ($products as $product) {
            $this->formatSingleProduct($product);
        }
    }

    private function formatSingleProduct($product)
    {
        $product->image_url = $product->image ? Storage::disk('public')->url($product->image) : null;
        $product->thumbnail_url = $product->thumbnail ? Storage::disk('public')->url($product->thumbnail) : null;
        if ($product->gallery) {
            $product->gallery_urls = array_map(function ($path) {
                return Storage::disk('public')->url($path);
            }, $product->gallery);
        }

        if ($product->vendor && $product->vendor->vendorProfile) {
            $profile = $product->vendor->vendorProfile;
            $profile->logo_url = $profile->logo ? Storage::disk('public')->url($profile->logo) : null;
            $profile->banner_url = $profile->banner ? Storage::disk('public')->url($profile->banner) : null;
        }

        if ($product->variants) {
            foreach ($product->variants as $variant) {
                $variant->image_url = $variant->image ? Storage::disk('public')->url($variant->image) : null;
            }
        }
    }
}

