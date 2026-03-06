<?php

namespace App\Services\Automation;

use App\Models\ShopSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WebhookService
{
    /**
     * Dispatch data to n8n based on vendor settings.
     */
    public function dispatch(int $userId, string $event, array $payload)
    {
        $setting = ShopSetting::where('user_id', $userId)
            ->where('group', 'ai_automation')
            ->where('key', 'webhooks')
            ->first();

        if (!$setting) {
            return;
        }

        $webhooks = json_decode($setting->value, true);
        $url = $webhooks[$event] ?? null;

        if (!$url) {
            return;
        }

        try {
            $response = Http::timeout(10)->post($url, [
                'event' => $event,
                'timestamp' => now()->toIso8601String(),
                'data' => $payload,
            ]);

            if ($response->failed()) {
                Log::error("n8n Webhook failed for event {$event}: " . $response->body());
            }
        } catch (\Exception $e) {
            Log::error("n8n Webhook exception for event {$event}: " . $e->getMessage());
        }
    }
}
