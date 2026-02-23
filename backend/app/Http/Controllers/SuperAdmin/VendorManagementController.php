<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VendorProfile;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class VendorManagementController extends Controller
{
    /**
     * List all vendors with their profiles and packages
     */
    public function index(Request $request)
    {
        $query = User::role('vendor')
            ->with(['vendorProfile.package', 'roles'])
            ->withCount(['vendorProfile']);

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->has('status')) {
            if ($request->status === 'active') {
                $query->whereHas('vendorProfile', fn($q) => $q->where('package_id', '!=', null));
            } elseif ($request->status === 'inactive') {
                $query->whereHas('vendorProfile', fn($q) => $q->whereNull('package_id'));
            }
        }

        $vendors = $query->latest()->paginate(15);

        return response()->json($vendors);
    }

    /**
     * Get a single vendor's full details
     */
    public function show(User $user)
    {
        return response()->json(
            $user->load(['vendorProfile.package', 'roles'])
        );
    }

    /**
     * Create a new vendor account
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'email'                => 'required|email|unique:users,email',
            'password'             => 'required|string|min:8',
            'store_name'           => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'address'              => 'nullable|string',
            'package_id'           => 'nullable|exists:packages,id',
            'package_expiry_date'  => 'nullable|date',
        ]);

        $user = User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->assignRole('vendor');

        VendorProfile::create([
            'user_id'              => $user->id,
            'store_name'           => $validated['store_name'] ?? $validated['name'] . "'s Store",
            'phone'                => $validated['phone'] ?? null,
            'address'              => $validated['address'] ?? null,
            'package_id'           => $validated['package_id'] ?? null,
            'package_expiry_date'  => $validated['package_expiry_date'] ?? null,
        ]);

        return response()->json([
            'message' => 'Vendor created successfully',
            'vendor'  => $user->load(['vendorProfile.package', 'roles']),
        ], 201);
    }

    /**
     * Update vendor information
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'                 => 'sometimes|required|string|max:255',
            'email'                => 'sometimes|required|email|unique:users,email,' . $user->id,
            'password'             => 'nullable|string|min:8',
            'store_name'           => 'nullable|string|max:255',
            'phone'                => 'nullable|string|max:20',
            'address'              => 'nullable|string',
            'package_id'           => 'nullable|exists:packages,id',
            'package_expiry_date'  => 'nullable|date',
        ]);

        $user->update([
            'name'     => $validated['name'] ?? $user->name,
            'email'    => $validated['email'] ?? $user->email,
            'password' => isset($validated['password']) ? Hash::make($validated['password']) : $user->password,
        ]);

        $user->vendorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'store_name'           => $validated['store_name'] ?? null,
                'phone'                => $validated['phone'] ?? null,
                'address'              => $validated['address'] ?? null,
                'package_id'           => $validated['package_id'] ?? null,
                'package_expiry_date'  => $validated['package_expiry_date'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Vendor updated successfully',
            'vendor'  => $user->fresh(['vendorProfile.package', 'roles']),
        ]);
    }

    /**
     * Assign a package to a vendor
     */
    public function assignPackage(Request $request, User $user)
    {
        $validated = $request->validate([
            'package_id'           => 'required|exists:packages,id',
            'package_expiry_date'  => 'nullable|date',
        ]);

        $package = Package::findOrFail($validated['package_id']);

        // Auto-set expiry based on package duration if not provided
        if (!isset($validated['package_expiry_date'])) {
            $expiry = now();
            if ($package->duration === 'monthly') {
                $expiry = $expiry->addMonth();
            } elseif ($package->duration === 'yearly') {
                $expiry = $expiry->addYear();
            } else {
                $expiry = null; // lifetime
            }
            $validated['package_expiry_date'] = $expiry;
        }

        $user->vendorProfile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'package_id'           => $validated['package_id'],
                'package_expiry_date'  => $validated['package_expiry_date'],
            ]
        );

        return response()->json([
            'message' => "Package '{$package->name}' assigned to {$user->name}",
            'vendor'  => $user->fresh(['vendorProfile.package']),
        ]);
    }

    /**
     * Quick-login as a vendor (impersonation by super-admin)
     */
    public function loginAsVendor(User $user)
    {
        if (!$user->hasRole('vendor')) {
            return response()->json(['message' => 'User is not a vendor'], 403);
        }

        // Revoke old impersonation tokens to keep things clean
        $user->tokens()->where('name', 'impersonate')->delete();

        $token = $user->createToken('impersonate')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => $user->load(['roles.permissions', 'vendorProfile.package']),
        ]);
    }

    /**
     * Delete a vendor
     */
    public function destroy(User $user)
    {
        if ($user->hasRole('super-admin')) {
            return response()->json(['message' => 'Cannot delete a super-admin'], 403);
        }

        $user->delete();

        return response()->json(['message' => 'Vendor deleted successfully']);
    }

    /**
     * Dashboard stats for super admin
     */
    public function dashboardStats()
    {
        $totalVendors    = User::role('vendor')->count();
        $activePackages  = VendorProfile::whereNotNull('package_id')->count();
        $totalPackages   = Package::count();
        $recentVendors   = User::role('vendor')
            ->with(['vendorProfile.package'])
            ->latest()
            ->take(5)
            ->get();

        $packageDistribution = Package::withCount('vendorProfiles')
            ->get()
            ->map(fn($p) => [
                'name'  => $p->name,
                'count' => $p->vendor_profiles_count,
                'price' => $p->price,
            ]);

        return response()->json([
            'total_vendors'         => $totalVendors,
            'vendors_with_packages' => $activePackages,
            'total_packages'        => $totalPackages,
            'recent_vendors'        => $recentVendors,
            'package_distribution'  => $packageDistribution,
        ]);
    }
}
