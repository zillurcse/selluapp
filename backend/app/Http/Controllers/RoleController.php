<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RoleController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:roles.view', only: ['index', 'show']),
            new Middleware('permission:roles.manage', only: ['store', 'update', 'destroy']),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $query = Role::with('permissions');

        if (!$user->hasRole('super-admin')) {
            $query->where('vendor_id', $user->id);
            // Optionally include common roles if needed, but per request, vendor creates his own
        }

        $roles = $query->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => [
                'required',
                \Illuminate\Validation\Rule::unique('roles', 'name')->where(function ($query) use ($user) {
                    if ($user->hasRole('vendor')) {
                        return $query->where('vendor_id', $user->id);
                    }
                    return $query->whereNull('vendor_id');
                })
            ],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        return DB::transaction(function () use ($request, $user) {
            $roleData = [
                'name' => $request->name, 
                'guard_name' => 'web'
            ];

            // If it's a vendor, assign vendor_id
            if ($user->hasRole('vendor')) {
                $roleData['vendor_id'] = $user->id;
            }

            $role = Role::create($roleData);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'message' => 'Role created successfully',
                'role' => $role->load('permissions')
            ], 201);
        });
    }

    public function show(Role $role)
    {
        $user = Auth::user();
        
        if (!$user->hasRole('super-admin') && $role->vendor_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($role->load('permissions'));
    }

    public function update(Request $request, Role $role)
    {
        $user = Auth::user();

        if (!$user->hasRole('super-admin') && $role->vendor_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => [
                'required',
                \Illuminate\Validation\Rule::unique('roles', 'name')->ignore($role->id)->where(function ($query) use ($user) {
                    if ($user->hasRole('vendor')) {
                        return $query->where('vendor_id', $user->id);
                    }
                    return $query->whereNull('vendor_id');
                })
            ],
            'permissions' => 'array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        return DB::transaction(function () use ($request, $role) {
            $role->update(['name' => $request->name]);
            
            if ($request->has('permissions')) {
                $role->syncPermissions($request->permissions);
            }

            return response()->json([
                'message' => 'Role updated successfully',
                'role' => $role->load('permissions')
            ]);
        });
    }

    public function destroy(Role $role)
    {
        $user = Auth::user();

        if (!$user->hasRole('super-admin')) {
            if ($role->vendor_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }
        }

        if ($role->name === 'super-admin' || $role->name === 'vendor') {
            return response()->json(['message' => 'Cannot delete system roles'], 403);
        }

        $role->delete();
        return response()->json(['message' => 'Role deleted successfully']);
    }
}
