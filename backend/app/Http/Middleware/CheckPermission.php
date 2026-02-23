<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Spatie\Permission\Exceptions\UnauthorizedException;
use Symfony\Component\HttpFoundation\Response;

/**
 * Custom permission middleware that wraps Spatie's PermissionMiddleware.
 * 
 * Vendor owners (users with a vendor_profile and no vendor_id) bypass
 * permission checks — they have full access to their vendor panel.
 * Super admins also bypass all checks.
 */
class CheckPermission
{
    public function handle(Request $request, Closure $next, $permission, $guard = null): Response
    {
        $user = $request->user($guard);

        if (!$user) {
            throw UnauthorizedException::notLoggedIn();
        }

        // Super Admin bypass
        if ($user->hasRole('super-admin')) {
            return $next($request);
        }

        // Vendor Owner bypass — they have full access to their own panel
        // (vendor_profile exists AND vendor_id is null means they ARE the vendor, not staff)
        if ($user->vendorProfile && !$user->vendor_id) {
            return $next($request);
        }

        // For staff and other users, check the actual permission
        $permissions = is_array($permission)
            ? $permission
            : explode('|', $permission);

        foreach ($permissions as $perm) {
            if ($user->can($perm)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forPermissions($permissions);
    }
}
