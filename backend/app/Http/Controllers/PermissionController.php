<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PermissionController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.view'),
        ];
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('super-admin')) {
            $permissions = Permission::all();
        } else {
            // Only allow non-admin permissions for vendors/staff
            $query = Permission::where('name', 'not like', 'admin.%');

            $profile = $user->shop_profile;
            $package = $profile ? $profile->package : null;

            // Default basic prefixes available to any vendor/staff
            $allowedPrefixes = [
                'roles', 'staff', 'settings', 'brands', 'units', 
                'categories', 'attributes', 'coupons'
            ];

            if ($package) {
                // Determine available features from the package
                $features = is_array($package->features) ? collect($package->features)->map(fn($f) => strtolower($f)) : collect([]);

                if ($features->contains(fn($f) => str_contains($f, 'products'))) {
                    $allowedPrefixes[] = 'products';
                }
                if ($features->contains(fn($f) => str_contains($f, 'orders'))) {
                    $allowedPrefixes[] = 'orders';
                }
                if ($features->contains(fn($f) => str_contains($f, 'customers'))) {
                    $allowedPrefixes[] = 'customers';
                }
                if ($features->contains(fn($f) => str_contains($f, 'promotions'))) {
                    $allowedPrefixes[] = 'promotions';
                }
                if ($features->contains(fn($f) => str_contains($f, 'fraud'))) {
                    $allowedPrefixes[] = 'fraud_check';
                }
                if ($features->contains(fn($f) => str_contains($f, 'reports'))) {
                    $allowedPrefixes[] = 'reports';
                }
                if ($package->pos_access || $features->contains(fn($f) => str_contains($f, 'pos'))) {
                    $allowedPrefixes[] = 'pos';
                }
                if ($package->hrm_access || $features->contains(fn($f) => str_contains($f, 'hrm'))) {
                    $allowedPrefixes[] = 'hrm';
                }
                if ($features->contains(fn($f) => str_contains($f, 'landing pages'))) {
                    $allowedPrefixes[] = 'landing_pages';
                }
                if ($features->contains(fn($f) => str_contains($f, 'delivery'))) {
                    $allowedPrefixes[] = 'delivery';
                }
            }

            $query->where(function($q) use ($allowedPrefixes) {
                foreach ($allowedPrefixes as $prefix) {
                    $q->orWhere('name', 'like', $prefix . '.%')
                      ->orWhere('name', $prefix);
                }
            });

            $permissions = $query->get();
        }

        // Group permissions by their prefix for better UI grouping
        $grouped = $permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        return response()->json($grouped);
    }
}
