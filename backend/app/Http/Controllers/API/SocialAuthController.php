<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\ShopSetting;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    /**
     * Handle Google OAuth login/register.
     * Validates the id_token from Google Sign-In (GSI),
     * then finds or creates a user, returning a Sanctum token.
     */
    public function googleLogin(Request $request)
    {
        $request->validate([
            'id_token' => 'required|string',
        ]);

        $domain   = $request->header('X-Tenant-Domain') ?? $request->query('domain');
        $tenantId = $this->resolveTenantId($domain);

        if (!$tenantId) {
            // Fallback to default vendor for development
            $tenantId = 5;
        }

        // Load vendor's Google Client ID to validate the token audience
        $settings = ShopSetting::where('user_id', $tenantId)
            ->where('group', 'marketing_social')
            ->get()
            ->pluck('value', 'key');

        $googleClientId = $settings->get('googleClientId');

        // Verify the id_token with Google's tokeninfo endpoint
        try {
            $response = Http::timeout(10)->get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $request->input('id_token'),
            ]);

            if (!$response->successful() || $response->json('error_description')) {
                return response()->json([
                    'message' => 'Invalid Google token',
                    'error'   => $response->json('error_description', 'Token verification failed'),
                ], 401);
            }

            $payload = $response->json();

            // Validate audience (aud) against vendor's configured Google Client ID
            if ($googleClientId && $payload['aud'] !== $googleClientId) {
                return response()->json(['message' => 'Token audience mismatch'], 401);
            }

            $googleId = $payload['sub'];
            $email    = $payload['email'];
            $name     = $payload['name'] ?? '';
            $picture  = $payload['picture'] ?? null;

            // Find existing user by google_id or by email
            $user = User::where('google_id', $googleId)->first()
                ?? User::where('email', $email)->first();

            if ($user) {
                // Update google_id if not yet set
                if (!$user->google_id) {
                    $user->google_id = $googleId;
                    $user->save();
                }
            } else {
                // Auto-register new customer + user
                $nameParts = explode(' ', $name, 2);
                $firstName = $nameParts[0] ?? '';
                $lastName  = $nameParts[1] ?? '';

                $customer = Customer::create([
                    'vendor_id'  => $tenantId,
                    'first_name' => $firstName,
                    'last_name'  => $lastName,
                    'email'      => $email,
                ]);

                $user = User::create([
                    'name'      => $name,
                    'email'     => $email,
                    'vendor_id' => $tenantId,
                    'google_id' => $googleId,
                    'password'  => Hash::make(Str::random(32)), // Random unusable password
                ]);

                $user->assignRole('customer');
            }

            $token = $user->createToken('google_auth_token')->plainTextToken;

            return response()->json([
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'user'         => new UserResource($user->load([
                    'roles:id,name',
                    'permissions:id,name',
                    'roles.permissions:id,name',
                ])),
            ]);

        } catch (\Exception $e) {
            Log::error('Google Social Auth error: ' . $e->getMessage());
            return response()->json(['message' => 'Authentication failed. Please try again.'], 500);
        }
    }

    private function resolveTenantId(?string $domain): ?int
    {
        if (!$domain) return null;

        $customSetting = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->first();

        if ($customSetting) return $customSetting->user_id;

        $parts = explode('.', $domain);
        if (count($parts) > 2) {
            $subdomain = $parts[0];
            $subSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'subDomain')
                ->where('value', $subdomain)
                ->first();
            if ($subSetting) return $subSetting->user_id;
        }

        return null;
    }
}
