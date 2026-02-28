<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Unit;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class UnitController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('permission:units.view', only: ['index', 'show']),
            new Middleware('permission:units.create', only: ['store']),
            new Middleware('permission:units.edit', only: ['update']),
            new Middleware('permission:units.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $units = Unit::orderBy('sort_order')->get();
        return response()->json([
            'data' => $units,
            'status' => 200
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        // Generate slug
        $baseSlug = Str::slug($request->name);
        $slug = $baseSlug;
        $count = 1;

        while (Unit::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count;
            $count++;
        }
        $validated['slug'] = $slug;

        // if (auth()->check()) {
        //    $validated['vendor_id'] = auth()->id();
        // }

        $unit = Unit::create($validated);
        return response()->json([
            'message' => 'Unit created successfully',
            'data' => $unit,
            'status' => 201
        ]);
    }

    public function show(Unit $unit)
    {
        return response()->json([
            'data' => $unit,
            'status' => 200
        ]);
    }

    public function update(Request $request, Unit $unit)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'symbol' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ]);

        if ($request->has('name') && $request->name !== $unit->name) {
            $baseSlug = Str::slug($request->name);
            $slug = $baseSlug;
            $count = 1;

            while (Unit::where('slug', $slug)->where('id', '!=', $unit->id)->exists()) {
                $slug = $baseSlug . '-' . $count;
                $count++;
            }
            $validated['slug'] = $slug;
        }

        $unit->update($validated);
        return response()->json([
            'message' => 'Unit updated successfully',
            'data' => $unit,
            'status' => 200
        ]);
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return response()->json(['message' => 'Unit deleted successfully']);
    }
}
