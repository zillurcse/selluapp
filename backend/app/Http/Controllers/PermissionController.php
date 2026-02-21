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
        $permissions = Permission::all();
        
        // Group permissions by their prefix for better UI grouping
        $grouped = $permissions->groupBy(function($permission) {
            $parts = explode('.', $permission->name);
            return count($parts) > 1 ? $parts[0] : 'general';
        });

        return response()->json($grouped);
    }
}
