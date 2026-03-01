<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;

class SteadfastService
{
    protected $baseUrl = 'https://portal.packzy.com/api/v1';

    public function __construct()
    {
        // Steadfast uses fixed base URL.
    }

    protected function getHeaders()
    {
        return [
            'Api-Key' => get_setting('steadfast_api_key'),
            'Secret-Key' => get_setting('steadfast_secret_key'),
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
        $response = Http::withHeaders($this->getHeaders())->post($this->baseUrl . '/create_order', $data);
        
        if ($response->successful()) {
            return $response->json();
        }

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
}
