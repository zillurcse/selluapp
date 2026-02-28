<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SupplierController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('permission:products.view', only: ['index', 'show']),
            new Middleware('permission:products.create', only: ['store']),
            new Middleware('permission:products.edit', only: ['update']),
            new Middleware('permission:products.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $vendor_id = auth()->id();
        $suppliers = Supplier::where('vendor_id', $vendor_id)->latest()->get();
        return response()->json([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $vendor_id = auth()->id();
        $supplier = Supplier::create(array_merge($request->all(), ['vendor_id' => $vendor_id]));

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier created successfully.',
            'data' => $supplier
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Supplier $supplier)
    {
        if ($supplier->vendor_id !== auth()->id()) {
            abort(403);
        }
        return response()->json([
            'status' => 'success',
            'data' => $supplier
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Supplier $supplier)
    {
        if ($supplier->vendor_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ]);

        $supplier->update($request->all());

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier updated successfully.',
            'data' => $supplier
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Supplier $supplier)
    {
        if ($supplier->vendor_id !== auth()->id()) {
            abort(403);
        }

        $supplier->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Supplier deleted successfully.'
        ]);
    }
}
