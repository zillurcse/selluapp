<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\ShopSetting;
use Illuminate\Http\Request;

class SitemapController extends Controller
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

        if ($customSetting) {
            return $customSetting->user_id;
        }

        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $subSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'subDomain')
                ->where('value', $subdomain)
                ->first();

            if ($subSetting) {
                return $subSetting->user_id;
            }
        }

        return null;
    }

    /**
     * Generate dynamic sitemap.xml for the storefront.
     */
    public function index(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);

        // Resolve the base store URL
        $storeUrl = $request->query('base_url', config('app.url'));
        $storeUrl = rtrim($storeUrl, '/');

        // Products
        $productsQuery = Product::where('is_active', true)->where('status', 'published')->latest();
        if ($tenantId) {
            $productsQuery->where('vendor_id', $tenantId);
        }
        $products = $productsQuery->select(['slug', 'updated_at'])->get();

        // Categories
        $categoriesQuery = Category::where('is_active', true)->whereNull('parent_id')->latest();
        $categories = $categoriesQuery->select(['slug', 'updated_at'])->get();

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        // Homepage
        $xml .= $this->buildUrl($storeUrl . '/', now()->toAtomString(), 'daily', '1.0');

        // Shop index
        $xml .= $this->buildUrl($storeUrl . '/shop', now()->toAtomString(), 'daily', '0.9');

        // Products
        foreach ($products as $product) {
            $xml .= $this->buildUrl(
                $storeUrl . '/shop/' . $product->slug,
                $product->updated_at->toAtomString(),
                'weekly',
                '0.8'
            );
        }

        // Categories
        foreach ($categories as $category) {
            $xml .= $this->buildUrl(
                $storeUrl . '/shop?category=' . $category->slug,
                $category->updated_at->toAtomString(),
                'weekly',
                '0.6'
            );
        }

        $xml .= '</urlset>';

        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    /**
     * Generate robots.txt.
     */
    public function robots(Request $request)
    {
        $storeUrl = $request->query('base_url', config('app.url'));
        $storeUrl = rtrim($storeUrl, '/');

        $content = "User-agent: *\n";
        $content .= "Allow: /\n";
        $content .= "Disallow: /account/\n";
        $content .= "Disallow: /checkout\n";
        $content .= "Disallow: /thank-you\n\n";
        $content .= "Sitemap: " . $storeUrl . "/api/sitemap.xml\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }

    private function buildUrl(string $loc, string $lastmod, string $changefreq, string $priority): string
    {
        return "  <url>\n"
            . "    <loc>" . htmlspecialchars($loc) . "</loc>\n"
            . "    <lastmod>{$lastmod}</lastmod>\n"
            . "    <changefreq>{$changefreq}</changefreq>\n"
            . "    <priority>{$priority}</priority>\n"
            . "  </url>\n";
    }
}
