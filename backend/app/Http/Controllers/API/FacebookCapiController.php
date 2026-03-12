<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiController extends Controller
{
    /**
     * Relay a Facebook Conversion API event (server-side) with hashed PII.
     * Handles deduplication by forwarding the event_id from the browser pixel.
     */
    public function event(Request $request)
    {
        $request->validate([
            'event_name'  => 'required|string',
            'event_id'    => 'required|string',    // UUID from browser pixel — deduplication key
        ]);

        $domain   = $request->header('X-Tenant-Domain') ?? $request->query('domain');
        $tenantId = $this->resolveTenantId($domain);

        if (!$tenantId) {
            return response()->json(['message' => 'Tenant not found'], 422);
        }

        // Load marketing settings for this vendor
        $settings = ShopSetting::where('user_id', $tenantId)
            ->where('group', 'marketing_social')
            ->get()
            ->pluck('value', 'key');

        $pixelId    = $settings->get('fbPixelId');
        $accessToken = $settings->get('fbPixelToken');
        $isActive = $settings->get('isFbPixelActive') !== false; // Default to true if not set

        if (!$isActive || !$pixelId || !$accessToken) {
            return response()->json(['message' => 'Facebook Pixel not configured or inactive'], 200);
        }

        // Hash PII with SHA-256 per Meta requirements
        $userData = $request->input('user_data', []);
        $hashedUserData = [];

        if (!empty($userData['email'])) {
            $hashedUserData['em'] = hash('sha256', strtolower(trim($userData['email'])));
        }
        if (!empty($userData['phone'])) {
            // Normalize: digits only
            $phone = preg_replace('/\D/', '', $userData['phone']);
            $hashedUserData['ph'] = hash('sha256', $phone);
        }
        if (!empty($userData['ip'])) {
            $hashedUserData['client_ip_address'] = $userData['ip'];
        }
        if (!empty($userData['user_agent'])) {
            $hashedUserData['client_user_agent'] = $userData['user_agent'];
        }
        if (!empty($userData['fbp'])) {
            $hashedUserData['fbp'] = $userData['fbp'];
        }
        if (!empty($userData['fbc'])) {
            $hashedUserData['fbc'] = $userData['fbc'];
        }
        if (!empty($userData['user_id'])) {
            $hashedUserData['external_id'] = hash('sha256', (string)$userData['user_id']);
        }

        $payload = [
            'data' => [
                [
                    'event_name'        => $request->input('event_name'),
                    'event_time'        => $request->input('event_time', time()),
                    'event_source_url'  => $request->input('event_source_url', ''),
                    'event_id'          => $request->input('event_id'),   // Deduplication
                    'action_source'     => 'website',
                    'user_data'         => $hashedUserData,
                    'custom_data'       => $request->input('custom_data', []),
                ]
            ]
        ];

        // Include test event code if configured
        $testEventId = $settings->get('fbTestEventId');
        if ($testEventId) {
            $payload['test_event_code'] = $testEventId;
        }

        try {
            $response = Http::timeout(10)->post(
                "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$accessToken}",
                $payload
            );

            Log::info('Facebook CAPI response', [
                'event'  => $request->input('event_name'),
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return response()->json([
                'success' => $response->successful(),
                'events_received' => $response->json('events_received', 0),
            ]);
        } catch (\Exception $e) {
            Log::error('Facebook CAPI error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => 'CAPI request failed'], 200);
        }
    }

    private function resolveTenantId(?string $domain): ?int
    {
        if (!$domain) return null;

        // Strip www. prefix to ensure clean database matches
        $domain = preg_replace('/^www\./', '', $domain);

        $customSetting = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->first();

        if ($customSetting) return $customSetting->user_id;

        $parts = explode('.', $domain);
        if (count($parts) >= 2) {
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
