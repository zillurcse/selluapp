<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SmsTemplate;
use App\Models\ShopSetting;

class SmsService
{
    protected $vendorId;

    public function __construct($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    public function send($to, $type, $data = [], $directMessage = null)
    {
        $config = $this->getConfig();
        
        if (!$config || empty($config['provider'])) {
            \Illuminate\Support\Facades\Log::error("SmsService: Missing config or provider for vendor {$this->vendorId}");
            return false;
        }

        if ($directMessage) {
            $message = $directMessage;
        } else {
            $template = SmsTemplate::where('user_id', $this->vendorId)
                ->where('type', $type)
                ->where('is_active', true)
                ->first();

            if (!$template) {
                \Illuminate\Support\Facades\Log::warning("SmsService: No active template found for type '{$type}' for vendor {$this->vendorId}");
                return false;
            }

            $message = $this->parseTemplate($template->content, $data);
        }

        switch ($config['provider']) {
            case 'SMS Bangladesh':
                return $this->sendSmsBangladesh($to, $message, $config);
            // Add other providers here
            default:
                return false;
        }
    }

    protected function getConfig()
    {
        $setting = ShopSetting::where('user_id', $this->vendorId)
            ->where('group', 'third_party_apis')
            ->where('key', 'sms')
            ->first();

        return $setting ? json_decode($setting->value, true) : null;
    }

    protected function parseTemplate($content, $data)
    {
        foreach ($data as $key => $value) {
            $content = str_replace("{{ $key }}", $value, $content);
        }
        return $content;
    }

    protected function sendSmsBangladesh($to, $message, $config)
    {
        $username = $config['username'] ?? '';
        $password = $config['password'] ?? '';
        $from = $config['senderId'] ?? 'maskingname';

        // Normalize phone number: Remove all non-numeric characters first
        $to = preg_replace('/[^0-9]/', '', $to);

        // Ensure it has 88 prefix if it's a Bangladesh number
        if (strlen($to) === 11 && str_starts_with($to, '0')) {
            $to = '88' . $to;
        } elseif (strlen($to) === 10) {
            $to = '880' . $to;
        }

        $response = Http::get('https://panel.smsbangladesh.com/api', [
            'user' => $username,
            'password' => $password,
            // 'from' => $from,
            'to' => $to,
            'text' => $message
        ]);

        if (!$response->successful()) {
            \Illuminate\Support\Facades\Log::error("SmsService: SMS Bangladesh API failed. Status: {$response->status()}, Body: {$response->body()}");
        }

        return $response->successful();
    }
}
