<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::withCount('vendorProfiles')->orderBy('price')->get();
        return response()->json($packages);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'required|numeric|min:0',
            'duration'        => 'required|in:monthly,yearly,lifetime',
            'product_limit'   => 'required|integer|min:-1',
            'order_limit'     => 'required|integer|min:-1',
            'employee_limit'  => 'required|integer|min:-1',
            'pos_access'      => 'boolean',
            'hrm_access'      => 'boolean',
            'is_active'       => 'boolean',
            'features'        => 'nullable|array',
        ]);

        $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(5);

        $package = Package::create($validated);

        return response()->json([
            'message' => 'Package created successfully',
            'package' => $package,
        ], 201);
    }

    public function show(Package $package)
    {
        return response()->json($package->loadCount('vendorProfiles'));
    }

    public function update(Request $request, Package $package)
    {
        $validated = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'description'     => 'nullable|string',
            'price'           => 'sometimes|required|numeric|min:0',
            'duration'        => 'sometimes|required|in:monthly,yearly,lifetime',
            'product_limit'   => 'sometimes|required|integer|min:-1',
            'order_limit'     => 'sometimes|required|integer|min:-1',
            'employee_limit'  => 'sometimes|required|integer|min:-1',
            'pos_access'      => 'boolean',
            'hrm_access'      => 'boolean',
            'is_active'       => 'boolean',
            'features'        => 'nullable|array',
        ]);

        $package->update($validated);

        return response()->json([
            'message' => 'Package updated successfully',
            'package' => $package->fresh(),
        ]);
    }

    public function destroy(Package $package)
    {
        if ($package->vendorProfiles()->count() > 0) {
            return response()->json([
                'message' => 'Cannot delete package with active vendors. Reassign vendors first.',
            ], 422);
        }

        $package->delete();

        return response()->json(['message' => 'Package deleted successfully']);
    }
}
