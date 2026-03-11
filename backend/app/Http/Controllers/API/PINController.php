<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class PINController extends Controller
{
    /**
     * Set or update security PIN for the authenticated user.
     */
    public function setPin(Request $request)
    {
        $request->validate([
            'pin' => 'required|string|size:5',
        ]);

        $user = $request->user();
        $user->security_pin = Hash::make($request->pin);
        $user->save();

        return response()->json([
            'message' => 'Security PIN updated successfully.'
        ]);
    }

    /**
     * Authenticate user using Email and PIN.
     */
    public function verifyPin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'pin' => 'required|string|size:5',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            throw ValidationException::withMessages([
                'email' => ['No account found with this email address.'],
            ]);
        }

        if (!$user->security_pin) {
            throw ValidationException::withMessages([
                'pin' => ['Security PIN has not been set for this account. Please login with password first.'],
            ]);
        }

        if (!Hash::check($request->pin, $user->security_pin)) {
            throw ValidationException::withMessages([
                'pin' => ['The provided PIN is incorrect.'],
            ]);
        }

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => new UserResource($user->load([
                'roles:id,name',
                'permissions:id,name',
                'roles.permissions:id,name',
                'vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit',
                'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'owner.vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit'
            ])),
        ]);
    }
}
