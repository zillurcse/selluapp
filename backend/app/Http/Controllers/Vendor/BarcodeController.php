<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\BarcodeService;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductBarcode;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BarcodeController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:products'),
            new Middleware('permission:barcodes.view', only: ['scan', 'printLabels']),
            new Middleware('permission:barcodes.generate', only: ['generate']),
            new Middleware('permission:barcodes.audit', only: ['audit', 'markAsPrinted']),
        ];
    }

    protected $barcodeService;

    public function __construct(BarcodeService $barcodeService)
    {
        $this->barcodeService = $barcodeService;
    }

    /**
     * Scan a barcode and return product/variant instantly.
     */
    public function scan(Request $request)
    {
        $request->validate(['code' => 'required|string']);
        
        $result = $this->barcodeService->lookup($request->code);

        if (!$result) {
            return response()->json(['message' => 'Barcode not found', 'status' => 'error'], 404);
        }

        // Return formatted result suitable for POS scan-to-cart
        $product = $result->product;
        $variant = $result->variant;
        
        $price = $variant ? $variant->price : $product->sale_price;
        $stock = $variant ? $variant->stock_qty : $product->stock_qty;
        $image = $variant && $variant->image ? $variant->image : $product->thumbnail;

        return response()->json([
            'status' => 'success',
            'data' => [
                'barcode_id' => $result->id,
                'barcode' => $result->barcode,
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'name' => $product->name . ($variant ? ' (' . $variant->sku . ')' : ''),
                'price' => $price,
                'stock_qty' => $stock,
                'image' => $image,
            ]
        ]);
    }

    /**
     * Auto-generate missing barcodes for a vendor's products.
     */
    public function generate(Request $request)
    {
        $vendor_id = auth()->id();
        $generatedCount = 0;

        // Fetch products and their variants for this vendor
        $products = Product::where('vendor_id', $vendor_id)
            ->with('variants')
            ->get();

        foreach ($products as $product) {
            if ($product->has_variants) {
                foreach ($product->variants as $variant) {
                    $exists = ProductBarcode::where('product_variant_id', $variant->id)->exists();
                    if (!$exists) {
                        $this->barcodeService->generateForProduct($product->id, $variant->id);
                        $generatedCount++;
                    }
                }
            } else {
                $exists = ProductBarcode::where('product_id', $product->id)->whereNull('product_variant_id')->exists();
                if (!$exists) {
                    $this->barcodeService->generateForProduct($product->id, null);
                    $generatedCount++;
                }
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => "Successfully generated {$generatedCount} missing barcodes."
        ]);
    }

    /**
     * Fetch requested products/barcodes and wrap in base64 images to be printed.
     */
    public function printLabels(Request $request)
    {
        $request->validate([
            'per_page' => 'nullable|integer',
            'search' => 'nullable|string',
            'status' => 'nullable|string|in:all,printed,not_printed'
        ]);

        $vendor_id = auth()->id();
        $query = ProductBarcode::with(['product', 'variant'])
            ->whereHas('product', function($q) use ($vendor_id) {
                $q->where('vendor_id', $vendor_id);
            });

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('is_printed', $request->status === 'printed');
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('barcode', 'like', "%{$search}%")
                  ->orWhereHas('product', function($pq) use ($search) {
                      $pq->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('variant', function($vq) use ($search) {
                      $vq->where('sku', 'like', "%{$search}%");
                  });
            });
        }

        $barcodes = $query->paginate($request->per_page ?? 30);

        // Append base64 image data to the result
        $barcodes->getCollection()->transform(function($item) {
            $item->barcode_image = $this->barcodeService->generateBarcodeImage($item->barcode, $item->symbology);
            return $item;
        });

        return response()->json([
            'status' => 'success',
            'data' => $barcodes
        ]);
    }

    /**
     * Warehouse Audit: submit array of scanned barcodes + quantities, return discrepancies.
     */
    public function audit(Request $request)
    {
        $request->validate([
            'scans' => 'required|array',
            'scans.*.barcode' => 'required|string',
            'scans.*.qty' => 'required|numeric|min:0',
        ]);

        $results = [];

        foreach ($request->scans as $scan) {
            $barcodeData = $this->barcodeService->lookup($scan['barcode']);
            if (!$barcodeData) {
                $results[] = [
                    'barcode' => $scan['barcode'],
                    'name' => 'Unknown Item',
                    'expected' => 0,
                    'scanned' => $scan['qty'],
                    'diff' => $scan['qty'],
                    'status' => 'unknown'
                ];
                continue;
            }

            $product = $barcodeData->product;
            $variant = $barcodeData->variant;

            $expectedQty = $variant ? $variant->stock_qty : $product->stock_qty;
            $diff = $scan['qty'] - $expectedQty;

            $status = 'match';
            if ($diff > 0) $status = 'surplus';
            if ($diff < 0) $status = 'missing';

            $results[] = [
                'barcode' => $scan['barcode'],
                'name' => $product->name . ($variant ? ' (' . $variant->sku . ')' : ''),
                'product_id' => $product->id,
                'variant_id' => $variant ? $variant->id : null,
                'expected' => $expectedQty,
                'scanned' => $scan['qty'],
                'diff' => $diff,
                'status' => $status
            ];
        }

        return response()->json([
            'status' => 'success',
            'data' => $results
        ]);
    }

    /**
     * Mark specific barcodes as printed.
     */
    public function markAsPrinted(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'integer|exists:product_barcodes,id'
        ]);

        $vendor_id = auth()->id();
        
        ProductBarcode::whereIn('id', $request->ids)
            ->whereHas('product', function($q) use ($vendor_id) {
                $q->where('vendor_id', $vendor_id);
            })
            ->update(['is_printed' => true]);

        return response()->json([
            'status' => 'success',
            'message' => 'Successfully marked barcodes as printed.'
        ]);
    }
}
