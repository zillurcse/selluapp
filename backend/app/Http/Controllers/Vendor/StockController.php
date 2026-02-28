<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Supplier;
use App\Models\StockLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class StockController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('permission:products.view', only: ['index', 'logs']),
            new Middleware('permission:products.edit', only: ['restock']),
        ];
    }

    /**
     * Get stock adjustment logs.
     */
    public function logs(Request $request)
    {
        $vendor_id = auth()->id();
        $logs = StockLog::with(['product', 'variant', 'supplier'])
            ->where('vendor_id', $vendor_id)
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json([
            'status' => 'success',
            'data' => $logs
        ]);
    }

    /**
     * Process restock for a product or variant.
     */
    public function restock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'nullable|exists:product_variants,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'quantity' => 'required|numeric|min:0.01',
            'note' => 'nullable|string|max:255',
            'purchase_price' => 'nullable|numeric|min:0',
        ]);

        $vendor_id = auth()->id();

        return DB::transaction(function () use ($request, $vendor_id) {
            $product = Product::where('id', $request->product_id)
                ->where('vendor_id', $vendor_id)
                ->firstOrFail();

            $oldStock = 0;
            $newStock = 0;
            $target = null;

            if ($request->variant_id) {
                $variant = ProductVariant::where('id', $request->variant_id)
                    ->where('product_id', $product->id)
                    ->firstOrFail();
                
                $oldStock = $variant->stock_qty;
                $variant->stock_qty += $request->quantity;
                
                if ($request->purchase_price) {
                    $variant->purchase_price = $request->purchase_price;
                }
                
                $variant->save();
                $newStock = $variant->stock_qty;
                $target = $variant;
            } else {
                $oldStock = $product->stock_qty;
                $product->stock_qty += $request->quantity;
                
                if ($request->purchase_price) {
                    $product->purchase_price = $request->purchase_price;
                }
                
                $product->save();
                $newStock = $product->stock_qty;
                $target = $product;
            }

            // Log the stock change
            StockLog::create([
                'vendor_id' => $vendor_id,
                'product_id' => $product->id,
                'product_variant_id' => $request->variant_id,
                'supplier_id' => $request->supplier_id,
                'type' => 'restock',
                'quantity' => $request->quantity,
                'old_stock' => $oldStock,
                'new_stock' => $newStock,
                'note' => $request->note,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Stock updated successfully.',
                'data' => [
                    'new_stock' => $newStock
                ]
            ]);
        });
    }

    /**
     * Supplier CRUD logic (minimal for now, can use useCrud in frontend)
     */
    public function suppliers(Request $request)
    {
        $vendor_id = auth()->id();
        $suppliers = Supplier::where('vendor_id', $vendor_id)->get();
        return response()->json([
            'status' => 'success',
            'data' => $suppliers
        ]);
    }
}
