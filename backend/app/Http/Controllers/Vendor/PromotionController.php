<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PromotionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:promotions'),
            new Middleware('permission:promotions.view', only: ['index', 'show']),
            new Middleware('permission:promotions.create', only: ['store']),
            new Middleware('permission:promotions.edit', only: ['update']),
            new Middleware('permission:promotions.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $promotions = \App\Models\Promotion::where('vendor_id', auth()->id())->latest()->get();
        return response()->json($promotions);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(\App\Http\Requests\StorePromotionRequest $request)
    {
        $validated = $request->validated();
        $validated['vendor_id'] = auth()->id();

        if ($request->hasFile('banner')) {
            $validated['banner'] = $request->file('banner')->store('vendors/promotions', 'public');
        }

        $promotion = \App\Models\Promotion::create($validated);
        return response()->json(['message' => 'Promotion created successfully', 'data' => $promotion], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $promotion = \App\Models\Promotion::where('vendor_id', auth()->id())->findOrFail($id);
        return response()->json($promotion);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\App\Http\Requests\UpdatePromotionRequest $request, string $id)
    {
        $promotion = \App\Models\Promotion::where('vendor_id', auth()->id())->findOrFail($id);
        $validated = $request->validated();

        if ($request->hasFile('banner')) {
            if ($promotion->banner) {
                \Illuminate\Support\Facades\Storage::delete('public/' . $promotion->banner);
            }
            $validated['banner'] = $request->file('banner')->store('vendors/promotions', 'public');
        }

        $promotion->update($validated);
        return response()->json(['message' => 'Promotion updated successfully', 'data' => $promotion]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $promotion = \App\Models\Promotion::where('vendor_id', auth()->id())->findOrFail($id);
        $promotion->delete();
        return response()->json(['message' => 'Promotion deleted successfully']);
    }
}
