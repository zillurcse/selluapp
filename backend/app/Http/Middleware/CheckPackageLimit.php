<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Product;
use App\Models\User;

class CheckPackageLimit
{
    /**
     * Handle an incoming request.
     *
     * Usage: middleware('package.limit:products')
     *        middleware('package.limit:orders')
     *        middleware('package.limit:employees')
     *
     * Only enforced on store/create actions.
     */
    public function handle(Request $request, Closure $next, string $limitType): Response
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Super Admin bypasses all limits
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        $profile = $user->shop_profile;

        if (!$profile || !$profile->package) {
            return response()->json([
                'message' => 'No active subscription package found.',
                'error_code' => 'NO_PACKAGE',
            ], 403);
        }

        $package = $profile->package;
        $vendorId = $user->vendor_id ?? $user->id;

        switch (strtolower($limitType)) {
            case 'products':
                $limit = $package->product_limit;
                if ($limit !== -1) {
                    $currentCount = Product::where('vendor_id', $vendorId)->count();
                    if ($currentCount >= $limit) {
                        return response()->json([
                            'message' => "Product limit reached ({$currentCount}/{$limit}). Please upgrade your plan.",
                            'error_code' => 'LIMIT_REACHED',
                            'limit_type' => 'products',
                            'current_count' => $currentCount,
                            'max_limit' => $limit,
                        ], 403);
                    }
                }
                break;

            case 'employees':
                $limit = $package->employee_limit;
                if ($limit !== -1) {
                    $currentCount = User::where('vendor_id', $vendorId)->count();
                    if ($currentCount >= $limit) {
                        return response()->json([
                            'message' => "Employee limit reached ({$currentCount}/{$limit}). Please upgrade your plan.",
                            'error_code' => 'LIMIT_REACHED',
                            'limit_type' => 'employees',
                            'current_count' => $currentCount,
                            'max_limit' => $limit,
                        ], 403);
                    }
                }
                break;

            case 'orders':
                // Orders are typically not limited on creation by vendor (customers create them)
                // but including for completeness
                $limit = $package->order_limit;
                if ($limit !== -1) {
                    $currentCount = \App\Models\Order::where('vendor_id', $vendorId)
                        ->whereMonth('created_at', now()->month)
                        ->count();
                    if ($currentCount >= $limit) {
                        return response()->json([
                            'message' => "Monthly order limit reached ({$currentCount}/{$limit}). Please upgrade your plan.",
                            'error_code' => 'LIMIT_REACHED',
                            'limit_type' => 'orders',
                            'current_count' => $currentCount,
                            'max_limit' => $limit,
                        ], 403);
                    }
                }
                break;
        }

        return $next($request);
    }
}
