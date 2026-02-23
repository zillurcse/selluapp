<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LandingPageController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:landing pages'),
            new Middleware('permission:landing_pages.view', only: ['index', 'show']),
            new Middleware('permission:landing_pages.create', only: ['store']),
            new Middleware('permission:landing_pages.edit', only: ['update']),
            new Middleware('permission:landing_pages.delete', only: ['destroy']),
        ];
    }

    public function index(Request $request)
    {
        $query = LandingPage::where('vendor_id', auth()->id());

        if ($request->has('type')) {
            $query->where('landing_page_type', $request->type);
        }

        return $query->with('product')->latest()->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'landing_page_type' => 'required|string',
            'product_id' => 'required|exists:products,id',
            'template_name' => 'required|string',
            'title' => 'required|string|max:255',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
        ]);

        $validated['vendor_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        $landingPage = LandingPage::create($validated);

        return response()->json([
            'message' => 'Landing page created successfully',
            'data' => $landingPage->load('product'),
            'status' => 201
        ], 201);
    }

    public function show($id)
    {
        $landingPage = LandingPage::findOrFail($id);
        
        if ($landingPage->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        return $landingPage->load('product');
    }

    public function update(Request $request, $id)
    {
        $landingPage = LandingPage::findOrFail($id);

        if ($landingPage->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'product_id' => 'nullable|exists:products,id',
            'template_name' => 'nullable|string',
            'title' => 'nullable|string|max:255',
            'settings' => 'nullable|array',
            'status' => 'nullable|string',
        ]);

        if (isset($validated['title']) && $validated['title'] !== $landingPage->title) {
            $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);
        }

        $landingPage->update($validated);

        return response()->json([
            'message' => 'Landing page updated successfully',
            'data' => $landingPage->load('product'),
            'status' => 200
        ]);
    }

    public function destroy($id)
    {
        $landingPage = LandingPage::findOrFail($id);

        if ($landingPage->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $landingPage->delete();

        return response()->json([
            'message' => 'Landing page deleted successfully',
            'status' => 200
        ]);
    }
}
