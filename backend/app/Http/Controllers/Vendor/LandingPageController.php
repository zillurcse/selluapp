<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\LandingPage;
use App\Models\Product;
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
        $type = $request->input('landing_page_type', 'single');

        $validated = $request->validate([
            'landing_page_type'  => 'required|string|in:single,multiple,common',
            'product_id'         => $this->productIdRules($type),
            'template_name'      => 'required|string',
            'title'              => 'required|string|max:255',
            'settings'           => 'nullable|array',
            'status'             => 'nullable|string|in:active,draft',
            'is_home'            => 'nullable|boolean',
            'campaign_start_at'  => 'nullable|date',
            'campaign_end_at'    => 'nullable|date|after_or_equal:campaign_start_at',
        ]);

        if ($type === 'common') {
            $validated['product_id'] = null;
        }

        if (!empty($validated['is_home'])) {
            LandingPage::where('vendor_id', auth()->id())->update(['is_home' => false]);
        }

        $validated['vendor_id'] = auth()->id();
        $validated['slug'] = $this->generateUniqueSlug($validated['title']);

        $landingPage = LandingPage::create($validated);

        return response()->json([
            'message' => 'Landing page created successfully',
            'data'    => $this->formatLandingPage($landingPage),
            'status'  => 201
        ], 201);
    }

    public function show($id)
    {
        $landingPage = LandingPage::findOrFail($id);

        if ($landingPage->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return $this->formatLandingPage($landingPage);
    }

    public function update(Request $request, $id)
    {
        $landingPage = LandingPage::findOrFail($id);

        if ($landingPage->vendor_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $type = $request->input('landing_page_type', $landingPage->landing_page_type);

        $validated = $request->validate([
            'landing_page_type'  => 'nullable|string|in:single,multiple,common',
            'product_id'         => $this->productIdRules($type, false),
            'template_name'      => 'nullable|string',
            'title'              => 'nullable|string|max:255',
            'settings'           => 'nullable|array',
            'status'             => 'nullable|string|in:active,draft',
            'is_home'            => 'nullable|boolean',
            'campaign_start_at'  => 'nullable|date',
            'campaign_end_at'    => 'nullable|date|after_or_equal:campaign_start_at',
        ]);

        if ($type === 'common') {
            $validated['product_id'] = null;
        }

        if (!empty($validated['is_home'])) {
            LandingPage::where('vendor_id', auth()->id())
                ->where('id', '!=', $id)
                ->update(['is_home' => false]);
        }

        if (isset($validated['title']) && $validated['title'] !== $landingPage->title) {
            $validated['slug'] = $this->generateUniqueSlug($validated['title'], $landingPage->id);
        }

        $landingPage->update($validated);

        return response()->json([
            'message' => 'Landing page updated successfully',
            'data'    => $this->formatLandingPage($landingPage->fresh()),
            'status'  => 200
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
            'status'  => 200
        ]);
    }

    private function productIdRules(string $type, bool $creating = true): string
    {
        if ($type === 'common') {
            return 'nullable|exists:products,id';
        }

        return ($creating ? 'required' : 'nullable') . '|exists:products,id';
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        do {
            $slug = Str::slug($title) . '-' . Str::random(6);
        } while (
            LandingPage::where('slug', $slug)
                ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
                ->exists()
        );

        return $slug;
    }

    private function formatLandingPage(LandingPage $landingPage): array
    {
        $landingPage->load('product');

        $data = $landingPage->toArray();
        $data['products'] = $this->resolveLinkedProducts($landingPage);

        return $data;
    }

    private function resolveLinkedProducts(LandingPage $landingPage)
    {
        $productIds = [];

        if ($landingPage->landing_page_type === 'multiple') {
            $productIds = $landingPage->settings['product_ids'] ?? [];
            if (empty($productIds) && $landingPage->product_id) {
                $productIds = [$landingPage->product_id];
            }
        } elseif ($landingPage->product_id) {
            $productIds = [$landingPage->product_id];
        }

        $productIds = array_values(array_filter($productIds));
        if (empty($productIds)) {
            return [];
        }

        $productMap = Product::where('vendor_id', auth()->id())
            ->whereIn('id', $productIds)
            ->get()
            ->keyBy('id');

        return collect($productIds)
            ->filter(fn ($id) => $productMap->has($id))
            ->map(fn ($id) => $productMap[$id])
            ->values();
    }
}
