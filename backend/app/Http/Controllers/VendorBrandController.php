<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VendorBrandController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('permission:brands.view', only: ['index', 'show']),
            new Middleware('permission:brands.create', only: ['store']),
            new Middleware('permission:brands.edit', only: ['update']),
            new Middleware('permission:brands.delete', only: ['destroy']),
            new Middleware('permission:brands.sort', only: ['updateOrder']),
        ];
    }

    public function index()
    {
        $brands = Brand::orderBy('sort_order')->get();
        return response()->json([
            'data' => $brands,
            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Generate slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        while (Brand::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('brands', 'public');
            $validated['image'] = $imagePath;
        }

        // if (auth()->check()) {
        //    $validated['vendor_id'] = auth()->id();
        // }

        $brand = Brand::create($validated);
        return response()->json([
            'message' => 'Brand created successfully',
            'data' => $brand,
            'status' => 201
        ]);
    }

    public function show(Brand $brand)
    {
        return response()->json([
            'data' => $brand,
            'status' => 200
        ]);
    }

    public function update(Request $request, Brand $brand)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->has('name') && $request->name !== $brand->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $count = 1;

            while (Brand::where('slug', $slug)->where('id', '!=', $brand->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($brand->image) {
                Storage::delete('public/' . $brand->image);
            }
            $imagePath = $request->file('image')->store('brands', 'public');
            $validated['image'] = $imagePath;
        }

        $brand->update($validated);
        return response()->json([
            'message' => 'Brand updated successfully',
            'data' => $brand,
            'status' => 200
        ]);
    }

    public function destroy(Brand $brand)
    {
        if ($brand->image) {
            Storage::delete('public/' . $brand->image);
        }
        $brand->delete();
        return response()->json(['message' => 'Brand deleted successfully']);
    }

    public function updateOrder(Request $request)
    {
        $data = $request->all();

        // Validate that we received an array
        if (!is_array($data)) {
            return response()->json(['message' => 'Invalid data format'], 422);
        }

        foreach ($data as $item) {
             if (isset($item['id']) && isset($item['sort_order'])) {
                Brand::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
             }
        }

        return response()->json(['message' => 'Brands reordered successfully']);
    }
}
