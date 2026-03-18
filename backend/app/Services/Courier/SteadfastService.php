<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;

class SteadfastService
{
    protected $baseUrl = 'https://portal.packzy.com/api/v1';
    protected $config;
    protected $vendorId;

    public function __construct($vendorId = null)
    {
        $this->vendorId = $vendorId;
        
        if ($vendorId) {
            $consolidated = get_vendor_setting($vendorId, 'steadfast', []);
            $this->config = [
                'api_key' => $consolidated['apiKey'] ?? ($consolidated['api_key'] ?? null),
                'secret_key' => $consolidated['secretKey'] ?? ($consolidated['secret_key'] ?? null),
            ];
        } else {
            $consolidated = get_setting('steadfast', []);
            $this->config = [
                'api_key' => $consolidated['apiKey'] ?? ($consolidated['api_key'] ?? null),
                'secret_key' => $consolidated['secretKey'] ?? ($consolidated['secret_key'] ?? null),
            ];
        }

        // Fallback to individual settings if needed
        if (empty($this->config['api_key'])) {
            $this->config['api_key'] = get_setting('steadfast_api_key');
        }
        if (empty($this->config['secret_key'])) {
            $this->config['secret_key'] = get_setting('steadfast_secret_key');
        }
    }

    protected function getHeaders()
    {
        return [
            'Api-Key' => $this->config['api_key'],
            'Secret-Key' => $this->config['secret_key'],
            'Content-Type' => 'application/json',
        ];
    }

    public function checkDeliveryCharge($data)
    {
        // Steadfast expects 'weight', 'address' (inside_dhaka, etc)
        $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl . '/check_delivery_charge', [
            'weight' => $data['weight'] ?? 1,
            'address' => $data['address'] ?? 'outside_dhaka',
        ]);

        if ($response->successful() && isset($response->json()['delivery_charge'])) {
            return $response->json()['delivery_charge'];
        }
        return null;
    }

    public function createOrder($data)
    {
        if (empty($this->config['api_key']) || empty($this->config['secret_key'])) {
            return ['error' => true, 'message' => 'Steadfast credentials missing.'];
        }
        $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl . '/create_order', $data);
        if ($response->successful()) {
            return $response->json();
        }

        \Illuminate\Support\Facades\Log::error("Steadfast API Failed. Status: " . $response->status() . " Body: " . $response->body());

        return [
            'error' => true,
            'status' => $response->status(),
            'message' => $response->json()['message'] ?? 'Steadfast API Error',
            'errors' => $response->json()['errors'] ?? null
        ];
    }

    public function getOrderStatus($consignmentId)
    {
        $response = Http::withHeaders($this->getHeaders())->get($this->baseUrl . "/status_by_cid/{$consignmentId}");
        return $response->successful() ? $response->json() : null;
    }

    public function getBalance()
    {
        $response = Http::withHeaders($this->getHeaders())->get($this->baseUrl . "/get_balance");
        return $response->successful() ? $response->json() : null;
    }

    public function getPoliceStations()
    {
        $response = Http::withHeaders($this->getHeaders())->get($this->baseUrl . "/police_stations");
        return $response->successful() ? $response->json() : null;
    }
}
