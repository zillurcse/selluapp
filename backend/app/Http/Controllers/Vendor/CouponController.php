<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CouponController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:coupons.view', only: ['index', 'show']),
            new Middleware('permission:coupons.create', only: ['store']),
            new Middleware('permission:coupons.edit', only: ['update']),
            new Middleware('permission:coupons.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $coupons = \App\Models\Coupon::where('vendor_id', auth()->id())->latest()->get();
        return response()->json($coupons);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StoreCouponRequest $request)
    {
        $validated = $request->validated();
        $validated['vendor_id'] = auth()->id();
        $coupon = \App\Models\Coupon::create($validated);
        return response()->json(['message' => 'Coupon created successfully', 'data' => $coupon], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $coupon = \App\Models\Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        return response()->json($coupon);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdateCouponRequest $request, string $id)
    {
        $coupon = \App\Models\Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        $coupon->update($request->validated());
        return response()->json(['message' => 'Coupon updated successfully', 'data' => $coupon]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $coupon = \App\Models\Coupon::where('vendor_id', auth()->id())->findOrFail($id);
        $coupon->delete();
        return response()->json(['message' => 'Coupon deleted successfully']);
    }
}
