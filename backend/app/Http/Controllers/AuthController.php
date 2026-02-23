<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $customer = Customer::create([
            'vendor_id' => 5,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        $user = User::create([
            'name' => $request->first_name . ' ' . $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user->load([
                'roles:id,name',
                'permissions:id,name',
                'roles.permissions:id,name',
                'vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'vendorProfile.package:id,name,pos_access,hrm_access,features',
                'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'owner.vendorProfile.package:id,name,pos_access,hrm_access,features'
            ])),
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials'],
            ]);
        }

        $user = User::where('email', $request->email)->firstOrFail();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user->load([
                'roles:id,name',
                'permissions:id,name',
                'roles.permissions:id,name',
                'vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'vendorProfile.package:id,name,pos_access,hrm_access,features',
                'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'owner.vendorProfile.package:id,name,pos_access,hrm_access,features'
            ])),
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function user(Request $request)
    {
        return new UserResource($request->user()->load([
            'roles:id,name',
            'permissions:id,name',
            'roles.permissions:id,name',
            'vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
            'vendorProfile.package:id,name,pos_access,hrm_access,features',
            'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
            'owner.vendorProfile.package:id,name,pos_access,hrm_access,features'
        ]));
    }
}
