<?php

namespace App\Http\Controllers;

use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Storage;

class AttributeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:products.view', only: ['index', 'show']),
            new Middleware('permission:products.create', only: ['store']),
            new Middleware('permission:products.edit', only: ['update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = Attribute::with('values');
        
        // Ensure vendors only see their own attributes or global attributes
        $vendorId = auth()->id();
        $user = auth()->user();
        
        // If there's an authenticated user and it's not a superadmin
        if ($user && $user->role !== 'superadmin') {
            $query->where(function($q) use ($vendorId) {
                $q->where('vendor_id', $vendorId)->orWhereNull('vendor_id');
            });
        }

        $attributes = $query->get();

        foreach ($attributes as $attribute) {
            if ($attribute->guide_image) {
                $attribute->guide_image_url = Storage::disk('public')->url($attribute->guide_image);
            }
        }

        return response()->json([
            'data' => $attributes,
            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:button,color,image,radio',
            'description' => 'nullable|string',
            'guide_image' => 'nullable|image|max:2048',
            'values' => 'required|string', // Expecting JSON string for FormData
        ]);

        $values = json_decode($validated['values'], true);

        $guideImagePath = null;
        if ($request->hasFile('guide_image')) {
            $guideImagePath = $request->file('guide_image')->store('attributes/guides', 'public');
        }

        $attribute = Attribute::create([
            'vendor_id' => auth()->id(),
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? null,
            'guide_image' => $guideImagePath
        ]);

        foreach ($values as $val) {
            $attribute->values()->create([
                'value' => $val['value'],
                'meta' => $val['meta'] ?? null
            ]);
        }

        return response()->json([
            'message' => 'Attribute created successfully',
            'data' => $attribute->load('values'),
            'status' => 201
        ]);
    }

    public function update(Request $request, Attribute $attribute)
    {
        $user = auth()->user();
        if ($user && $attribute->vendor_id !== $user->id && $user->role !== 'superadmin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:button,color,image,radio',
            'description' => 'nullable|string',
            'guide_image' => 'nullable|image|max:2048',
            'values' => 'required|string',
        ]);

        $values = json_decode($validated['values'], true);

        if ($request->hasFile('guide_image')) {
            if ($attribute->guide_image) {
                Storage::disk('public')->delete($attribute->guide_image);
            }
            $attribute->guide_image = $request->file('guide_image')->store('attributes/guides', 'public');
        }

        $attribute->update([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'description' => $validated['description'] ?? $attribute->description,
            'guide_image' => $attribute->guide_image
        ]);

        // Sync values
        $keptIds = [];
        foreach ($values as $val) {
            if (isset($val['id'])) {
                $attrVal = AttributeValue::where('id', $val['id'])->where('attribute_id', $attribute->id)->first();
                if ($attrVal) {
                    $attrVal->update(['value' => $val['value'], 'meta' => $val['meta'] ?? null]);
                    $keptIds[] = $attrVal->id;
                }
            } else {
                $newAttr = $attribute->values()->create([
                    'value' => $val['value'],
                    'meta' => $val['meta'] ?? null
                ]);
                $keptIds[] = $newAttr->id;
            }
        }
        
        $attribute->values()->whereNotIn('id', $keptIds)->delete();

        return response()->json([
            'message' => 'Attribute updated successfully',
            'data' => $attribute->load('values'),
            'status' => 200
        ]);
    }

    public function show(Attribute $attribute)
    {
        $user = auth()->user();
        if ($user && $attribute->vendor_id !== $user->id && $user->role !== 'superadmin' && $attribute->vendor_id !== null) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        if ($attribute->guide_image) {
            $attribute->guide_image_url = Storage::disk('public')->url($attribute->guide_image);
        }

        return response()->json([
            'data' => $attribute->load('values'),
            'status' => 200
        ]);
    }

    public function destroy(Attribute $attribute)
    {
        $user = auth()->user();
        if ($user && $attribute->vendor_id !== $user->id && $user->role !== 'superadmin') {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        $attribute->delete();
        return response()->noContent();
    }
}
