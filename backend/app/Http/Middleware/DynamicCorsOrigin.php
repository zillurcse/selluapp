<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
            return app(\App\Services\ShopDomainService::class)
                ->resolveUserIdFromDomain($domain) !== null;
        });
    }
}
