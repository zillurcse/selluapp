<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VendorAttributeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:attributes.view', only: ['index', 'show']),
            new Middleware('permission:attributes.create', only: ['store']),
            new Middleware('permission:attributes.edit', only: ['update']),
            new Middleware('permission:attributes.delete', only: ['destroy']),
            new Middleware('permission:attributes.sort', only: ['updateOrder']),
        ];
    }

    public function index()
    {
        $categories = Category::orderBy('sort_order', 'asc')->get();
        return response()->json([
            'data' => $categories,
            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        // Generate base slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        // Ensure uniqueness
        while (Category::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }
        $validated['slug'] = $slug;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->hasFile('icon')) {
            $iconPath = $request->file('icon')->store('categories/icons', 'public');
            $validated['icon'] = $iconPath;
        }

        // Add vendor_id logic if applicable
        // if (auth()->check()) {
        //    $validated['vendor_id'] = auth()->id();
        // }

        $category = Category::create($validated);
        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
            'status' => 201
        ]);
    }

    public function show(Category $category)
    {
        return response()->json([
            'data' => $category,
            'status' => 200
        ]);
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'icon' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'parent_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ]);

        if ($request->has('name') && $request->name !== $category->name) {
             // Generate base slug
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $count = 1;

            // Ensure uniqueness
            while (Category::where('slug', $slug)->where('id', '!=', $category->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        if ($request->hasFile('image')) {
            if ($category->image) {
                Storage::delete('public/' . $category->image);
            }
            $imagePath = $request->file('image')->store('categories', 'public');
            $validated['image'] = $imagePath;
        }

        if ($request->hasFile('icon')) {
            if ($category->icon) {
                Storage::delete('public/' . $category->icon);
            }
            $iconPath = $request->file('icon')->store('categories/icons', 'public');
            $validated['icon'] = $iconPath;
        }

        $category->update($validated);
        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
            'status' => 200
        ]);
    }

    public function destroy(Category $category)
    {
        if ($category->image) {
            Storage::delete('public/' . $category->image);
        }
        $category->delete();
        return response()->json([
            'message' => 'Category deleted successfully',
            'status' => 200
        ]);
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
                Category::where('id', $item['id'])->update(['sort_order' => $item['sort_order']]);
            }
        }

        return response()->json(['message' => 'Categories reordered successfully']);
    }
}