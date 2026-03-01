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

    public function __construct()
    {
        // Load config from individual settings OR consolidated 'pathao' JSON
        $consolidated = get_setting('pathao', []);
        
        $this->config = [
            'client_id' => get_setting('pathao_client_id', $consolidated['clientId'] ?? ($consolidated['client_id'] ?? null)),
            'client_secret' => get_setting('pathao_client_secret', $consolidated['clientSecret'] ?? ($consolidated['client_secret'] ?? null)),
            'username' => get_setting('pathao_username', $consolidated['email'] ?? ($consolidated['username'] ?? null)),
            'password' => get_setting('pathao_password', $consolidated['password'] ?? null),
            'store_id' => get_setting('pathao_store_id', $consolidated['storeId'] ?? ($consolidated['store_id'] ?? null)),
            'sandbox' => get_setting('pathao_sandbox', $consolidated['sandbox'] ?? 0),
        ];

        $this->baseUrl = $this->config['sandbox'] == 1 
            ? 'https://courier-api-sandbox.pathao.com' 
            : 'https://api-hermes.pathao.com';

        // Add version path if not present but needed by API
        // For Pathao, endpoints like /cities are under /aladdin/api/v1/
        $this->apiBaseUrl = $this->baseUrl . '/aladdin/api/v1';

        $this->token = $this->getToken();
    }

    protected function getToken()
    {
        $token = cache('pathao_token');
        if ($token) return $token;

        $payload = [
            'client_id' => $this->config['client_id'],
            'client_secret' => $this->config['client_secret'],
            'username' => $this->config['username'],
            'password' => $this->config['password'],
            'grant_type' => 'password',
        ];

        \Log::info("Pathao getToken request to: " . $this->baseUrl . '/aladdin/api/v1/issue-token');
        
        $response = Http::post($this->baseUrl . '/aladdin/api/v1/issue-token', $payload);

        if ($response->successful()) {
            $data = $response->json();
            \Log::info("Pathao getToken success.");
            cache(['pathao_token' => $data['access_token']], $data['expires_in'] - 60);
            return $data['access_token'];
        }

        \Log::error("Pathao getToken failed. Status: " . $response->status());
        \Log::error("Pathao getToken response: " . $response->body());

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
