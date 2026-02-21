<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class VendorStaffController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:hrm.employees.view', only: ['index', 'show']),
            new Middleware('permission:hrm.employees.create', only: ['store']),
            new Middleware('permission:hrm.employees.edit', only: ['update']),
            new Middleware('permission:hrm.employees.delete', only: ['destroy']),
        ];
    }

    public function index()
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;
        
        $staff = User::where('vendor_id', $vendorId)
            ->with('roles')
            ->latest()
            ->get();

        return response()->json($staff);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;

        $staff = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'vendor_id' => $vendorId,
        ]);

        $staff->assignRole($request->role);

        return response()->json([
            'message' => 'Staff member created successfully',
            'user' => $staff->load('roles')
        ], 201);
    }

    public function show(User $staff)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;

        if ($staff->vendor_id !== $vendorId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        return response()->json($staff->load('roles'));
    }

    public function update(Request $request, User $staff)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;

        if ($staff->vendor_id !== $vendorId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($staff->id),
            ],
            'password' => 'nullable|string|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        $staff->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        if ($request->filled('password')) {
            $staff->update(['password' => Hash::make($request->password)]);
        }

        $staff->syncRoles([$request->role]);

        return response()->json([
            'message' => 'Staff member updated successfully',
            'user' => $staff->load('roles')
        ]);
    }

    public function destroy(User $staff)
    {
        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;

        if ($staff->vendor_id !== $vendorId) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $staff->delete();
        return response()->json(['message' => 'Staff member deleted successfully']);
    }
}
