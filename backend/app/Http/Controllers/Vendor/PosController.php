<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\PosSale;
use App\Models\PosSaleItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PosController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:pos.view', only: ['products', 'customers', 'categories', 'brands', 'sales', 'stats']),
            new Middleware('permission:pos.manage', only: ['placeOrder']),
        ];
    }

    /**
     * Get products for POS (with filters).
     */
    public function products(Request $request): JsonResponse
    {
        $query = Product::with(['categories', 'brand', 'unit'])
            ->where('vendor_id', $request->user()->id)
            ->where('status', 'published')
            ->where('is_active', true);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id') && $request->category_id !== 'all') {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('categories.id', $request->category_id);
            });
        }

        if ($request->filled('brand_id') && $request->brand_id !== 'all') {
            $query->where('brand_id', $request->brand_id);
        }

        $products = $query->latest()->get()->map(function ($product) {
            return [
                'id'       => $product->id,
                'name'     => $product->name,
                'sku'      => $product->sku,
                'price'    => (float) ($product->discount_price ?: $product->sale_price),
                'stock'    => (int) $product->stock_qty,
                'image'    => $product->image ? Storage::disk('public')->url($product->image) : null,
                'category' => $product->categories->first()?->name,
                'brand'    => $product->brand?->name,
            ];
        });

        return response()->json($products);
    }

    /**
     * Get customers for POS.
     */
    public function customers(Request $request): JsonResponse
    {
        $customers = Customer::where('vendor_id', $request->user()->id)
            ->select('id', 'first_name', 'last_name', 'email', 'phone')
            ->latest()
            ->get()
            ->map(function ($c) {
                return [
                    'id'    => $c->id,
                    'name'  => trim($c->first_name . ' ' . $c->last_name),
                    'phone' => $c->phone,
                    'email' => $c->email,
                ];
            });

        // Prepend walk-in customer
        $customers->prepend([
            'id'    => 'walking',
            'name'  => 'Walk-in Customer',
            'phone' => '',
            'email' => '',
        ]);

        return response()->json($customers);
    }

    /**
     * Get categories for POS filter.
     */
    public function categories(Request $request): JsonResponse
    {
        // Get only categories that have products for this vendor
        $vendorId = $request->user()->id;

        $categories = Category::whereHas('products', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)->where('status', 'published')->where('is_active', true);
        })->select('id', 'name')->get();

        return response()->json($categories);
    }

    /**
     * Get brands for POS filter.
     */
    public function brands(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;

        $brands = Brand::whereHas('products', function ($q) use ($vendorId) {
            $q->where('vendor_id', $vendorId)->where('status', 'published')->where('is_active', true);
        })->select('id', 'name')->get();

        return response()->json($brands);
    }

    /**
     * Place a POS order.
     */
    public function placeOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'customer_id'    => 'nullable',
            'cart'           => 'required|array|min:1',
            'cart.*.id'      => 'required|integer|exists:products,id',
            'cart.*.name'    => 'required|string',
            'cart.*.sku'     => 'nullable|string',
            'cart.*.price'   => 'required|numeric|min:0',
            'cart.*.qty'     => 'required|integer|min:1',
            'discount_type'  => 'nullable|in:fixed,percentage',
            'discount_value' => 'nullable|numeric|min:0',
            'tax_percentage' => 'nullable|numeric|min:0|max:100',
            'shipping'       => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'note'           => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $subtotal = collect($validated['cart'])->sum(fn($item) => $item['price'] * $item['qty']);

            $discountType  = $validated['discount_type'] ?? 'fixed';
            $discountValue = (float)($validated['discount_value'] ?? 0);
            $discountAmount = $discountType === 'percentage'
                ? round($subtotal * $discountValue / 100, 2)
                : $discountValue;

            $taxPercentage = (float)($validated['tax_percentage'] ?? 0);
            $taxAmount     = round(($subtotal - $discountAmount) * $taxPercentage / 100, 2);
            $shipping      = (float)($validated['shipping'] ?? 0);
            $total         = round($subtotal - $discountAmount + $taxAmount + $shipping, 2);

            $customerId = null;
            if (!empty($validated['customer_id']) && $validated['customer_id'] !== 'walking') {
                $customerId = (int) $validated['customer_id'];
            }

            $sale = PosSale::create([
                'vendor_id'       => $request->user()->id,
                'customer_id'     => $customerId,
                'reference'       => 'SA_' . strtoupper(Str::random(8)),
                'subtotal'        => $subtotal,
                'discount_type'   => $discountType,
                'discount_value'  => $discountValue,
                'discount_amount' => $discountAmount,
                'tax_percentage'  => $taxPercentage,
                'tax_amount'      => $taxAmount,
                'shipping'        => $shipping,
                'total'           => $total,
                'payment_method'  => $validated['payment_method'] ?? 'cash',
                'status'          => 'paid',
                'note'            => $validated['note'] ?? null,
            ]);

            foreach ($validated['cart'] as $item) {
                PosSaleItem::create([
                    'pos_sale_id'  => $sale->id,
                    'product_id'   => $item['id'],
                    'product_name' => $item['name'],
                    'sku'          => $item['sku'] ?? null,
                    'qty'          => $item['qty'],
                    'price'        => $item['price'],
                    'subtotal'     => round($item['price'] * $item['qty'], 2),
                ]);

                // Deduct stock
                Product::where('id', $item['id'])->decrement('stock_qty', $item['qty']);
            }

            DB::commit();

            return response()->json([
                'message'   => 'Order placed successfully',
                'reference' => $sale->reference,
                'total'     => $sale->total,
                'sale_id'   => $sale->id,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to place order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get recent sales for this vendor.
     */
    public function sales(Request $request): JsonResponse
    {
        $sales = PosSale::with(['customer', 'items'])
            ->where('vendor_id', $request->user()->id)
            ->latest()
            ->limit(50)
            ->get()
            ->map(function ($sale) {
                return [
                    'id'             => $sale->id,
                    'reference'      => $sale->reference,
                    'date'           => $sale->created_at->format('d M Y, h:i A'),
                    'customer_name'  => $sale->customer
                        ? trim($sale->customer->first_name . ' ' . $sale->customer->last_name)
                        : 'Walk-in Customer',
                    'total'          => (float) $sale->total,
                    'payment_method' => $sale->payment_method,
                    'status'         => $sale->status,
                    'items'          => $sale->items->map(fn($i) => [
                        'name'     => $i->product_name,
                        'qty'      => $i->qty,
                        'price'    => (float) $i->price,
                        'subtotal' => (float) $i->subtotal,
                    ]),
                ];
            });

        return response()->json($sales);
    }

    /**
     * Register stats (today's summary).
     */
    public function stats(Request $request): JsonResponse
    {
        $vendorId = $request->user()->id;
        $today = now()->toDateString();

        $todaySales = PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->whereDate('created_at', $today)
            ->selectRaw('
                COUNT(*) as total_orders,
                SUM(total) as total_sales,
                SUM(CASE WHEN payment_method = "cash" THEN total ELSE 0 END) as cash_total,
                SUM(CASE WHEN payment_method = "card" THEN total ELSE 0 END) as card_total,
                SUM(CASE WHEN payment_method NOT IN ("cash","card") THEN total ELSE 0 END) as other_total
            ')
            ->first();

        return response()->json([
            'total_orders' => (int) ($todaySales->total_orders ?? 0),
            'total_sales'  => (float) ($todaySales->total_sales ?? 0),
            'cash_total'   => (float) ($todaySales->cash_total ?? 0),
            'card_total'   => (float) ($todaySales->card_total ?? 0),
            'other_total'  => (float) ($todaySales->other_total ?? 0),
            'total_refund' => 0,
        ]);
    }
}
