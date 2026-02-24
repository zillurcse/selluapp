<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('package.limit:products', only: ['store']),
            new Middleware('permission:products.view', only: ['index', 'show', 'slugByStatusProducts']),
            new Middleware('permission:products.create', only: ['store']),
            new Middleware('permission:products.edit', only: ['update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Product::with(['categories', 'brand', 'unit']);

        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->has('category_id') && $request->category_id) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        // Filter by brand
        if ($request->has('brand_id') && $request->brand_id) {
            $query->where('brand_id', $request->brand_id);
        }

        // Filter by search query
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }
        
        // Filter by active status
        if ($request->has('is_active') && $request->is_active !== null) {
             $query->where('is_active', $request->is_active);
        }

        $perPage = $request->get('limit') ?? $request->get('per_page');

        if ($perPage) {
            $products = $query->latest()->paginate($perPage);
            $items = $products->items();
        } else {
            $products = $query->latest()->get();
            $items = $products;
        }

        foreach ($items as $product) {
            $product->image = $product->image ? Storage::disk('public')->url($product->image) : null;
            $product->thumbnail = $product->thumbnail ? Storage::disk('public')->url($product->thumbnail) : null;
            $product->video = $product->video ? Storage::disk('public')->url($product->video) : null;
            if ($product->gallery) {
                $product->gallery = array_map(function ($path) {
                    return Storage::disk('public')->url($path);
                }, $product->gallery);
            }
        }
        return $products;
    }

    public function store(\App\Http\Requests\StoreProductRequest $request)
    {
        $validated = $request->validated();

        $categoryIds = $validated['category_ids'];
        unset($validated['category_ids']);

        // Handle JSON fields
        if (isset($validated['faqs'])) {
            $validated['faqs'] = json_decode($validated['faqs'], true);
        }
        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true);
        }

        $validated['vendor_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);

        // Handle File Uploads
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            $validated['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        // Handle Gallery Uploads with ordering support
        if ($request->has('gallery_items')) {
            $galleryItems = json_decode($request->gallery_items, true);
            $newGalleryFiles = $request->file('gallery', []);
            $galleryPaths = [];

            foreach ($galleryItems as $item) {
                if ($item['source'] === 'upload' && isset($newGalleryFiles[$item['index']])) {
                    $file = $newGalleryFiles[$item['index']];
                    $galleryPaths[] = $file->store('products/gallery', 'public');
                }
            }
            $validated['gallery'] = $galleryPaths;
        } elseif ($request->hasFile('gallery')) {
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $product = Product::create($validated);
        $product->categories()->sync($categoryIds);

        return response()->json([
            'message' => 'Product created successfully',
            'data' => $product->load(['categories', 'brand', 'unit']),
            'status' => 201
        ], 201);
    }

    public function show(Product $product)
    {
        $product->image = $product->image ? Storage::disk('public')->url($product->image) : null;
        $product->thumbnail = $product->thumbnail ? Storage::disk('public')->url($product->thumbnail) : null;
        $product->video = $product->video ? Storage::disk('public')->url($product->video) : null;
        
        if ($product->gallery) {
            $product->gallery = array_map(function ($path) {
                return Storage::disk('public')->url($path);
            }, $product->gallery);
        } else {
            $product->gallery = [];
        }

        return $product->load(['categories', 'brand', 'unit']);
    }

    public function update(\App\Http\Requests\UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if (isset($validated['category_ids'])) {
            $product->categories()->sync($validated['category_ids']);
            unset($validated['category_ids']);
        }

        // Handle JSON fields
        if (isset($validated['faqs'])) {
            $validated['faqs'] = json_decode($validated['faqs'], true);
        }
        if (isset($validated['key_features'])) {
            $validated['key_features'] = json_decode($validated['key_features'], true);
        }
        if (isset($validated['specifications'])) {
            $validated['specifications'] = json_decode($validated['specifications'], true);
        }

        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        }

        // Handle File Uploads with cleanup
        if ($request->hasFile('image')) {
            if ($product->image) Storage::disk('public')->delete($product->image);
            $validated['image'] = $request->file('image')->store('products/images', 'public');
        }

        if ($request->hasFile('thumbnail')) {
            if ($product->thumbnail) Storage::disk('public')->delete($product->thumbnail);
            $validated['thumbnail'] = $request->file('thumbnail')->store('products/thumbnails', 'public');
        }

        if ($request->hasFile('video')) {
            if ($product->video) Storage::disk('public')->delete($product->video);
            $validated['video'] = $request->file('video')->store('products/videos', 'public');
        }

        // Handle Gallery Uploads with ordering support
        if ($request->has('gallery_items')) {
            $galleryItems = json_decode($request->gallery_items, true);
            $newGalleryFiles = $request->file('gallery', []);
            $finalGallery = [];
            $oldGallery = $product->gallery ?? [];
            $currentlyUsedPaths = [];

            foreach ($galleryItems as $item) {
                if ($item['source'] === 'existing') {
                    // Extract path from URL if it's a full URL
                    $path = $item['value'];
                    if (str_contains($path, '/storage/')) {
                        $path = Str::after($path, '/storage/');
                    }
                    $finalGallery[] = $path;
                    $currentlyUsedPaths[] = $path;
                } elseif ($item['source'] === 'upload' && isset($newGalleryFiles[$item['index']])) {
                    $file = $newGalleryFiles[$item['index']];
                    $path = $file->store('products/gallery', 'public');
                    $finalGallery[] = $path;
                }
            }

            // Cleanup: Delete old files that are no longer in the gallery
            foreach ($oldGallery as $oldPath) {
                if (!in_array($oldPath, $currentlyUsedPaths)) {
                    Storage::disk('public')->delete($oldPath);
                }
            }

            $validated['gallery'] = $finalGallery;
        } elseif ($request->hasFile('gallery')) {
            // Fallback for simple uploads without ordering
            if ($product->gallery) {
                foreach ($product->gallery as $path) {
                    Storage::disk('public')->delete($path);
                }
            }
            
            $galleryPaths = [];
            foreach ($request->file('gallery') as $file) {
                $galleryPaths[] = $file->store('products/gallery', 'public');
            }
            $validated['gallery'] = $galleryPaths;
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Product updated successfully',
            'data' => $product->load(['categories', 'brand', 'unit']),
            'status' => 200
        ]);
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->noContent();
    }

    //draft products
    public function slugByStatusProducts($slug)
    {
        $products = Product::where('status', $slug)->get();
        return $products;
    }
}
