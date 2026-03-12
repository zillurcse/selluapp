<?php

namespace App\Services;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FacebookCapiService
{
    /**
     * Send a server-side event to Facebook Conversion API.
     *
     * @param string $eventName
     * @param array $userData
     * @param array $customData
     * @param string $eventId
     * @param int $tenantId
     * @return bool
     */
    public function sendEvent(string $eventName, array $userData, array $customData, string $eventId, int $tenantId): bool
    {
        // Load marketing settings for this vendor
        $settings = ShopSetting::where('user_id', $tenantId)
            ->where('group', 'marketing_social')
            ->get()
            ->pluck('value', 'key');

        $pixelId    = $settings->get('fbPixelId');
        $accessToken = $settings->get('fbPixelToken');

        if (!$pixelId || !$accessToken) {
            return false;
        }

        // Hash PII with SHA-256
        $hashedUserData = [];

        if (!empty($userData['email'])) {
            $hashedUserData['em'] = hash('sha256', strtolower(trim($userData['email'])));
        }
        if (!empty($userData['phone'])) {
            $phone = preg_replace('/\D/', '', $userData['phone']);
            $hashedUserData['ph'] = hash('sha256', $phone);
        }
        if (!empty($userData['first_name'])) {
            $hashedUserData['fn'] = hash('sha256', strtolower(trim($userData['first_name'])));
        }
        if (!empty($userData['last_name'])) {
            $hashedUserData['ln'] = hash('sha256', strtolower(trim($userData['last_name'])));
        }
        if (!empty($userData['city'])) {
            $hashedUserData['ct'] = hash('sha256', strtolower(trim($userData['city'])));
        }
        if (!empty($userData['country'])) {
            $hashedUserData['country'] = hash('sha256', strtolower(trim($userData['country'])));
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
                    'event_name'        => $eventName,
                    'event_time'        => time(),
                    'event_source_url'  => $userData['event_source_url'] ?? '',
                    'event_id'          => $eventId,
                    'action_source'     => 'website',
                    'user_data'         => $hashedUserData,
                    'custom_data'       => $customData,
                ]
            ]
        ];

        // Include test event code if configured
        $testEventId = $settings->get('fbTestEventId');
        if ($testEventId) {
            $payload['test_event_code'] = $testEventId;
        }

        try {
            $response = Http::timeout(5)->post(
                "https://graph.facebook.com/v19.0/{$pixelId}/events?access_token={$accessToken}",
                $payload
            );

            Log::info("Facebook CAPI [$eventName] response", [
                'status' => $response->status(),
                'body'   => $response->json(),
            ]);

            return $response->successful();
        } catch (\Exception $e) {
            Log::error('Facebook CAPI error: ' . $e->getMessage());
            return false;
        }
    }
}
