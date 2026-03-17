<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function sendMagicLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ]);

        $user = User::where('email', $request->email)->firstOrFail();
        $vendorId = $request->header('X-Vendor-Id') ?: 5; // Use ?: to catch empty strings

        // Generate a signed URL that expires in 30 minutes
        $signedUrl = URL::temporarySignedRoute(
            'magic-link.verify',
            now()->addMinutes(30),
            ['email' => $user->email, 'vendor_id' => $vendorId]
        );

        // Swap the API domain/path with the frontend domain/path
        $frontendUrl = env('FRONTEND_URL', 'http://localhost:3000');
        $magicLink = str_replace(url('/api/auth/magic-link/verify'), $frontendUrl . '/login/verify', $signedUrl);

        \Illuminate\Support\Facades\Log::info("Magic Link Debug: VendorID={$vendorId}, UserID={$user->id}, Link={$magicLink}");

        try {
            \App\Helpers\EmailHelper::setMailConfig($vendorId);
            
            $emailData = [
                'name' => $user->name,
                'magic_link' => $magicLink,
                'expires_in' => '30 minutes',
                'shop_name' => $user->shop_profile?->store_name ?? env('APP_NAME'),
            ];

            \Illuminate\Support\Facades\Mail::to($user->email)->send(new \App\Mail\ShopMail($vendorId, 'magic_link_login', $emailData));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Magic Link Email Error: ' . $e->getMessage());
            return response()->json([
                'message' => 'Magic link generated but email sending failed.',
                'link' => env('APP_DEBUG') ? $magicLink : null
            ], 200);
        }

        return response()->json(['message' => 'Magic link sent to your email!']);
    }

    public function verifyMagicLink(Request $request)
    {
        \Illuminate\Support\Facades\Log::info("Magic Link Verification Attempt: " . $request->fullUrl());
        \Illuminate\Support\Facades\Log::info("Request Params: " . json_encode($request->all()));

        // Use false to ignore the host/absolute URL, checking only path and parameters
        if (!$request->hasValidSignature(false)) {
            \Illuminate\Support\Facades\Log::warning("Magic Link Signature Validation FAILED (lenient check) for: " . $request->fullUrl());
            return response()->json([
                'message' => 'Invalid or expired magic link.',
                'debug_url' => $request->fullUrl(),
                'debug_params' => $request->all()
            ], 401);
        }

        \Illuminate\Support\Facades\Log::info("Magic Link Signature Validation SUCCESS for: " . $request->email);

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Log in the user
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

    public function register(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:8|confirmed',
        ]);


        $vendorId = $request->header('X-Vendor-Id') ?? 5; // Use header or fallback

        $customer = Customer::create([
            'vendor_id' => $vendorId,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone' => $request->phone,
        ]);

        if (!$customer) {
            return response()->json(['message' => 'Failed to create customer'], 500);
        }
        $user = User::create([
            'name' => $customer->first_name . ' ' . $customer->last_name,
            'email' => $customer->email,
            'vendor_id' => $vendorId,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('customer');

        $token = $user->createToken('auth_token')->plainTextToken;

        // Send Welcome Email
        try {
            \App\Helpers\EmailHelper::setMailConfig($vendorId);
            
            $emailData = [
                'first_name' => $customer->first_name,
                'last_name' => $customer->last_name,
                'email' => $customer->email,
                'shop_name' => $user->owner?->vendorProfile?->store_name ?? env('APP_NAME'),
            ];

            \Illuminate\Support\Facades\Mail::to($customer->email)->send(new \App\Mail\ShopMail($vendorId, 'welcome_email', $emailData));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Welcome Email Error: ' . $e->getMessage());
        }

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
                'vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit',
                'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
                'owner.vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit'
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
            'vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit',
            'owner.vendorProfile:id,user_id,package_id,store_name,store_slug,logo',
            'owner.vendorProfile.package:id,name,pos_access,hrm_access,features,product_limit'
        ]));
    }
}
