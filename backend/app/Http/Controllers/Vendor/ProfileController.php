<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProfileController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view', only: ['index']),
            new Middleware('permission:settings.manage', only: ['update']),
        ];
    }

    public function index()
    {
        $user = auth()->user();

        if ($user->vendor_id) {
            $owner = User::with('vendorProfile')->find($user->vendor_id);
            return response()->json($owner);
        }
        return response()->json($user->load('vendorProfile'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            // User Basic Info
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20', // Storing in vendor_profile but validation here

            // Password
            'current_password' => 'nullable|required_with:new_password',
            'new_password' => 'nullable|min:8|confirmed',

            // Shop Info
            'store_name' => 'nullable|string|max:255',
            'store_slug' => 'nullable|string|max:255|unique:vendor_profiles,store_slug,' . ($user->vendorProfile->id ?? 'NULL'),
            'description' => 'nullable|string',
            'email' => 'nullable|email|max:255', // Store email
            'address' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'banner' => 'nullable|image|max:2048',

            // Socials
            'facebook' => 'nullable|string|max:255',
            'twitter' => 'nullable|string|max:255',
            'instagram' => 'nullable|string|max:255',
            'youtube' => 'nullable|string|max:255',
        ]);

        // 1. Update User Name
        $user->name = $request->name;

        // 2. Update Password
        if ($request->filled('current_password') && $request->filled('new_password')) {
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['message' => 'Current password does not match'], 422);
            }
            $user->password = Hash::make($request->new_password);
        }
        $user->save();

        // 3. Update/Create Vendor Profile
        $profile = $user->vendorProfile ?? new VendorProfile(['user_id' => $user->id]);

        $profile->fill([
            'store_name' => $request->store_name,
            'phone' => $request->phone,
            'email' => $request->email,
            'address' => $request->address,
            'description' => $request->description,
            'facebook' => $request->facebook,
            'twitter' => $request->twitter,
            'instagram' => $request->instagram,
            'youtube' => $request->youtube,
        ]);

        // Handle Slug
        if ($request->store_name && !$request->store_slug) {
             $profile->store_slug = Str::slug($request->store_name);
        } else if ($request->store_slug) {
             $profile->store_slug = Str::slug($request->store_slug);
        }

        // Handle File Uploads
        if ($request->hasFile('logo')) {
            if ($profile->logo) Storage::delete('public/' . $profile->logo);
            $profile->logo = $request->file('logo')->store('vendors/logos', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($profile->banner) Storage::delete('public/' . $profile->banner);
            $profile->banner = $request->file('banner')->store('vendors/banners', 'public');
        }

        $profile->save();

        return response()->json(['message' => 'Profile updated successfully', 'user' => $user->load('vendorProfile')]);
    }
}
