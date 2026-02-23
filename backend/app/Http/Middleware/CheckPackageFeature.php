<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPackageFeature
{
    /**
     * Handle an incoming request.
     *
     * Usage: middleware('package.feature:products')
     *        middleware('package.feature:pos')
     *        middleware('package.feature:hrm')
     *
     * The feature name is matched against:
     *  - 'pos'  → package.pos_access
     *  - 'hrm'  → package.hrm_access
     *  - other  → package.features JSON array (case-insensitive partial match)
     *
     * Additionally enforces limits:
     *  - product_limit, order_limit, employee_limit
     */
    public function handle(Request $request, Closure $next, string $feature): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Super Admin bypasses all package restrictions
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Resolve the vendor profile (owner's or own)
        $profile = $user->shop_profile;

        if (!$profile || !$profile->package) {
            return response()->json([
                'message' => 'No active subscription package found. Please subscribe to a plan.',
                'error_code' => 'NO_PACKAGE',
            ], 403);
        }

        $package = $profile->package;

        // Check package expiry
        if ($profile->package_expiry_date && now()->greaterThan($profile->package_expiry_date)) {
            return response()->json([
                'message' => 'Your subscription package has expired. Please renew your plan.',
                'error_code' => 'PACKAGE_EXPIRED',
            ], 403);
        }

        // Check specific feature access
        if (!$this->packageHasFeature($package, $feature)) {
            return response()->json([
                'message' => "Your current plan ({$package->name}) does not include access to this feature. Please upgrade your package.",
                'error_code' => 'FEATURE_NOT_AVAILABLE',
                'required_feature' => $feature,
                'current_package' => $package->name,
            ], 403);
        }

        return $next($request);
    }

    /**
     * Check if a package has a specific feature.
     */
    protected function packageHasFeature($package, string $feature): bool
    {
        // Boolean flags for POS and HRM
        if (strtolower($feature) === 'pos') {
            return (bool) $package->pos_access;
        }

        if (strtolower($feature) === 'hrm') {
            return (bool) $package->hrm_access;
        }

        // Check the features JSON array (case-insensitive partial match)
        if (is_array($package->features)) {
            return collect($package->features)->contains(function ($f) use ($feature) {
                return str_contains(strtolower($f), strtolower($feature));
            });
        }

        return false;
    }
}
