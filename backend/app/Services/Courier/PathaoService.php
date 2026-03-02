<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;

class PathaoService
{
    protected $baseUrl;
    protected $apiBaseUrl;
    protected $token;
    protected $config;

    public function __construct($vendorId = null)
    {
        // Load config from individual settings OR consolidated 'pathao' JSON
        // If vendorId is provided, look into shop_settings/business_settings for that vendor
        if ($vendorId) {
            $consolidated = get_vendor_setting($vendorId, 'pathao', []);
            $this->config = [
                'client_id' => get_vendor_setting($vendorId, 'pathao_client_id', $consolidated['clientId'] ?? ($consolidated['client_id'] ?? null)),
                'client_secret' => get_vendor_setting($vendorId, 'pathao_client_secret', $consolidated['clientSecret'] ?? ($consolidated['client_secret'] ?? null)),
                'username' => get_vendor_setting($vendorId, 'pathao_username', $consolidated['email'] ?? ($consolidated['username'] ?? null)),
                'password' => get_vendor_setting($vendorId, 'pathao_password', $consolidated['password'] ?? null),
                'store_id' => get_vendor_setting($vendorId, 'pathao_store_id', $consolidated['storeId'] ?? ($consolidated['store_id'] ?? null)),
                'sandbox' => get_vendor_setting($vendorId, 'pathao_sandbox', $consolidated['sandbox'] ?? 0),
            ];
        } else {
            $consolidated = get_setting('pathao', []);
            $this->config = [
                'client_id' => get_setting('pathao_client_id', $consolidated['clientId'] ?? ($consolidated['client_id'] ?? null)),
                'client_secret' => get_setting('pathao_client_secret', $consolidated['clientSecret'] ?? ($consolidated['client_secret'] ?? null)),
                'username' => get_setting('pathao_username', $consolidated['email'] ?? ($consolidated['username'] ?? null)),
                'password' => get_setting('pathao_password', $consolidated['password'] ?? null),
                'store_id' => get_setting('pathao_store_id', $consolidated['storeId'] ?? ($consolidated['store_id'] ?? null)),
                'sandbox' => get_setting('pathao_sandbox', $consolidated['sandbox'] ?? 0),
            ];
        }

        $this->baseUrl = $this->config['sandbox'] == 1 
            ? 'https://courier-api-sandbox.pathao.com' 
            : 'https://api-hermes.pathao.com';

        $this->apiBaseUrl = $this->baseUrl . '/aladdin/api/v1';

        $this->token = $this->getToken($vendorId);
    }

    protected function getToken($vendorId = null)
    {
        $cacheKey = $vendorId ? "pathao_token_{$vendorId}" : "pathao_token";
        $token = cache($cacheKey);
        if ($token) return $token;

        if (empty($this->config['client_id']) || empty($this->config['client_secret']) || empty($this->config['username']) || empty($this->config['password'])) {
            \Log::error("Pathao credentials missing for vendor: " . ($vendorId ?? 'SYSTEM'));
            return null;
        }

        $payload = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'grant_type' => 'password',
        ];

        \Log::info("Pathao getToken request for " . ($vendorId ?? 'SYSTEM') . " to: " . $this->baseUrl . '/aladdin/api/v1/issue-token');
        
        try {
            $response = Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', $payload);

            if ($response->successful()) {
                $data = $response->json();
                \Log::info("Pathao getToken success for " . ($vendorId ?? 'SYSTEM'));
                cache([$cacheKey => $data['access_token']], $data['expires_in'] - 60);
                return $data['access_token'];
            }

            \Log::error("Pathao getToken failed. Status: " . $response->status());
            \Log::error("Pathao getToken response: " . $response->body());
        } catch (\Exception $e) {
            \Log::error("Pathao getToken exception: " . $e->getMessage());
        }

        return null;
    }

    public function getCities()
    {
        if (!$this->token) return ['data' => ['data' => []]];
        $response = Http::withToken($this->token)->get($this->apiBaseUrl . '/city-list');
        \Log::info("Pathao getCities response: " . $response->body());
        return $response->successful() ? $response->json() : ['data' => ['data' => []]];
    }


    public function getZones($cityId)
    {
        if (!$this->token) return ['data' => ['data' => []]];
        $response = Http::withToken($this->token)->get($this->apiBaseUrl . "/cities/{$cityId}/zone-list");
        return $response->successful() ? $response->json() : ['data' => ['data' => []]];
    }


    public function getAreas($zoneId)
    {
        if (!$this->token) return ['data' => ['data' => []]];
        $response = Http::withToken($this->token)->get($this->apiBaseUrl . "/zones/{$zoneId}/area-list");
        return $response->successful() ? $response->json() : ['data' => ['data' => []]];
    }


    public function calculateCost($data)
    {
        if (!$this->token) return null;
        
        // Trim store_id just in case
        if (isset($data['store_id'])) {
            $data['store_id'] = trim($data['store_id']);
        }
        
        \Log::info("Pathao API Request Data: " . json_encode($data));
        
        $response = Http::withToken($this->token)->post($this->apiBaseUrl . '/merchant/price-plan', $data);
        
        if ($response->successful()) {
            $result = $response->json();
            \Log::info("Pathao API Success Response: " . json_encode($result));
            
            // Pathao sometimes returns 200 even on errors like "Unauthorized!"
            if (isset($result['error']) && $result['error'] === true) {
                \Log::error("Pathao API Error Message: " . ($result['message'] ?? 'Unknown error'));
                return null;
            }
            // Use final_price if available, fallback to price
            return $result['data']['final_price'] ?? ($result['data']['price'] ?? null);
        } else {
            \Log::error("Pathao API Failed status: " . $response->status());
            \Log::error("Pathao API Failed Body: " . $response->body());
        }
        return null;
    }


    public function createOrder($data)
    {
        if (!$this->token) return ['error' => true, 'message' => 'No Pathao Token available. Check credentials.'];
        
        $response = Http::withToken($this->token)->post($this->apiBaseUrl . '/orders', $data);
        
        if ($response->successful()) {
            return $response->json()['data'];
        }

        // Return error details if failed
        return [
            'error' => true,
            'status' => $response->status(),
            'errors' => $response->json()['errors'] ?? null,
            'message' => $response->json()['message'] ?? 'Pathao API Error'
        ];
    }


    public function getOrderStatus($orderId)
    {
        if (!$this->token) return null;
        $response = Http::withToken($this->token)->get($this->apiBaseUrl . "/orders/{$orderId}/info");
        return $response->successful() ? $response->json()['data'] : null;
    }

    public function getStoreId()
    {
        return $this->config['store_id'];
    }

}
