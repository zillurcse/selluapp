<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\ShopSetting;
use Symfony\Component\HttpFoundation\Response;

class DynamicCorsOrigin
{
    /**
     * Check if the request origin is a registered vendor domain and
     * patch the CORS allowed_origins config before HandleCors runs.
     *
     * This must be registered BEFORE the built-in HandleCors middleware.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $origin = $request->headers->get('Origin');

        if ($origin && !$this->isAlreadyAllowed($origin)) {
            $domain = preg_replace('/^https?:\/\//', '', rtrim($origin, '/'));
            $domain = preg_replace('/^www\./', '', $domain);

            if ($this->isRegisteredVendorDomain($domain)) {
                $allowed = config('cors.allowed_origins', []);
                $allowed[] = $origin;
                config(['cors.allowed_origins' => $allowed]);
            }
        }

        return $next($request);
    }

    private function isAlreadyAllowed(string $origin): bool
    {
        $allowed = config('cors.allowed_origins', []);
        return in_array($origin, $allowed) || in_array('*', $allowed);
    }

    private function isRegisteredVendorDomain(string $domain): bool
    {
        // Cache for 10 minutes to avoid DB hit on every request
        $cacheKey = 'cors_domain_' . md5($domain);

        return Cache::remember($cacheKey, 600, function () use ($domain) {
            // Check custom domain
            $isCustom = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'customDomain')
                ->where('value', $domain)
                ->exists();

            if ($isCustom) {
                return true;
            }

            // Check subdomain — extract the first segment
            $parts = explode('.', $domain);
            if (count($parts) >= 2) {
                $subdomain = $parts[0];
                return ShopSetting::where('group', 'shop_domain')
                    ->where('key', 'subDomain')
                    ->where('value', $subdomain)
                    ->exists();
            }

            return false;
        });
    }
}
