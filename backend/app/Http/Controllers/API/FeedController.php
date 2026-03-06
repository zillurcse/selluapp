<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FeedController extends Controller
{
    private function resolveTenantId(Request $request)
    {
        $domain = $request->header('X-Tenant-Domain') ?? $request->query('domain');

        if (!$domain) {
            return null;
        }

        $customSetting = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->first();

        if ($customSetting) return $customSetting->user_id;

        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $subSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'subDomain')
                ->where('value', $subdomain)
                ->first();
            if ($subSetting) return $subSetting->user_id;
        }

        return null;
    }

    private function getProducts(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);
        $query = Product::with(['categories', 'brand'])
            ->where('is_active', true)
            ->where('status', 'published');

        if ($tenantId) {
            $query->where('vendor_id', $tenantId);
        }

        return [$query->get(), $tenantId];
    }

    /**
     * Google Merchant Center Shopping Feed (XML).
     */
    public function googleShopping(Request $request)
    {
        [$products, $tenantId] = $this->getProducts($request);

        $storeUrl = $request->query('base_url', config('app.url'));
        $storeUrl = rtrim($storeUrl, '/');

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . PHP_EOL;
        $xml .= '  <channel>' . PHP_EOL;
        $xml .= '    <title>Google Shopping Feed</title>' . PHP_EOL;
        $xml .= '    <link>' . htmlspecialchars($storeUrl) . '</link>' . PHP_EOL;
        $xml .= '    <description>Product feed for Google Merchant Center</description>' . PHP_EOL;

        foreach ($products as $product) {
            $imageUrl = $product->image ? Storage::disk('public')->url($product->image) : '';
            $price = number_format((float)($product->sale_price ?? $product->regular_price ?? 0), 2, '.', '');
            $currency = 'BDT';
            $availability = ($product->stock_qty ?? 0) > 0 ? 'in stock' : 'out of stock';
            $category = $product->categories->first()?->name ?? '';
            $brand = $product->brand?->name ?? '';
            $productUrl = $storeUrl . '/shop/' . $product->slug;

            $xml .= '    <item>' . PHP_EOL;
            $xml .= '      <g:id>' . htmlspecialchars((string)$product->id) . '</g:id>' . PHP_EOL;
            $xml .= '      <g:title>' . htmlspecialchars($product->name) . '</g:title>' . PHP_EOL;
            $xml .= '      <g:description>' . htmlspecialchars(strip_tags($product->short_description ?? $product->description ?? $product->name)) . '</g:description>' . PHP_EOL;
            $xml .= '      <g:link>' . htmlspecialchars($productUrl) . '</g:link>' . PHP_EOL;
            $xml .= '      <g:image_link>' . htmlspecialchars($imageUrl) . '</g:image_link>' . PHP_EOL;
            $xml .= '      <g:availability>' . $availability . '</g:availability>' . PHP_EOL;
            $xml .= '      <g:price>' . $price . ' ' . $currency . '</g:price>' . PHP_EOL;
            if ($brand) {
                $xml .= '      <g:brand>' . htmlspecialchars($brand) . '</g:brand>' . PHP_EOL;
            }
            if ($category) {
                $xml .= '      <g:product_type>' . htmlspecialchars($category) . '</g:product_type>' . PHP_EOL;
            }
            $xml .= '      <g:condition>new</g:condition>' . PHP_EOL;
            if ($product->sku) {
                $xml .= '      <g:mpn>' . htmlspecialchars($product->sku) . '</g:mpn>' . PHP_EOL;
            }
            $xml .= '    </item>' . PHP_EOL;
        }

        $xml .= '  </channel>' . PHP_EOL;
        $xml .= '</rss>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Facebook Product Data Feed (CSV).
     */
    public function facebookFeed(Request $request)
    {
        [$products, $tenantId] = $this->getProducts($request);

        $storeUrl = $request->query('base_url', config('app.url'));
        $storeUrl = rtrim($storeUrl, '/');

        $rows   = [];
        $rows[] = ['id', 'title', 'description', 'availability', 'condition', 'price', 'link', 'image_link', 'brand'];

        foreach ($products as $product) {
            $imageUrl = $product->image ? Storage::disk('public')->url($product->image) : '';
            $price = number_format((float)($product->sale_price ?? $product->regular_price ?? 0), 2, '.', '') . ' BDT';
            $availability = ($product->stock_qty ?? 0) > 0 ? 'in stock' : 'out of stock';
            $brand = $product->brand?->name ?? '';
            $productUrl = $storeUrl . '/shop/' . $product->slug;
            $description = strip_tags($product->short_description ?? $product->description ?? $product->name);

            $rows[] = [
                $product->id,
                $product->name,
                $description,
                $availability,
                'new',
                $price,
                $productUrl,
                $imageUrl,
                $brand,
            ];
        }

        $output = fopen('php://temp', 'r+');
        foreach ($rows as $row) {
            fputcsv($output, $row);
        }
        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return response($csv, 200)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="facebook-feed.csv"');
    }
}
