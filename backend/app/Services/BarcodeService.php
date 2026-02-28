<?php

namespace App\Services;

use App\Models\ProductBarcode;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Picqer\Barcode\BarcodeGeneratorPNG;

class BarcodeService
{
    /**
     * Look up a product or variant by barcode.
     * Uses Redis caching for ultra-fast (<10ms) response times.
     */
    public function lookup(string $barcode)
    {
        $cacheKey = 'barcode_lookup_' . $barcode;

        return Cache::remember($cacheKey, now()->addDay(), function () use ($barcode) {
            return ProductBarcode::with(['product:id,name,vendor_id,slug', 'variant:id,sku,price,stock_qty'])
                ->where('barcode', $barcode)
                ->first();
        });
    }

    /**
     * Clear barcode cache.
     */
    public function clearCache(string $barcode)
    {
        Cache::forget('barcode_lookup_' . $barcode);
    }

    /**
     * Generate a unique barcode string avoiding collisions.
     */
    public function generateUniqueCode(): string
    {
        return DB::transaction(function () {
            do {
                // Generate a random 12 digit code (EAN13 without checksum, or CODE128 compatible)
                // Let's use a 12 digit format suitable for CODE128
                $code = str_pad(mt_rand(1, 999999999999), 12, '0', STR_PAD_LEFT);
            } while (ProductBarcode::where('barcode', $code)->lockForUpdate()->exists());

            return $code;
        });
    }

    /**
     * Generate a barcode for a product or variant.
     */
    public function generateForProduct($product_id, $variant_id = null): ProductBarcode
    {
        $code = $this->generateUniqueCode();

        $barcode = ProductBarcode::create([
            'product_id' => $product_id,
            'product_variant_id' => $variant_id,
            'barcode' => $code,
            'symbology' => 'CODE128',
        ]);

        return $barcode;
    }

    /**
     * Generate base64 string for the PDF or UI using picqer/php-barcode-generator.
     */
    public function generateBarcodeImage(string $barcode, string $symbology = 'CODE128'): string
    {
        $generator = new BarcodeGeneratorPNG();
        $type = $symbology === 'EAN13' ? $generator::TYPE_EAN_13 : $generator::TYPE_CODE_128;
        
        $image = $generator->getBarcode($barcode, $type, 2, 60);

        return base64_encode($image);
    }
}
