<?php

namespace App\Services\Courier;

use Illuminate\Support\Facades\Http;
use App\Models\BusinessSetting;
use SteadFast\SteadFastCourierLaravelPackage\Facades\SteadfastCourier;

class SteadfastService
{
    protected $baseUrl;
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

        // Keep local base URL for manual calls if needed
        $this->baseUrl = config('steadfast-courier.base_url', 'https://portal.packzy.com/api/v1');
    }

    /**
     * Dynamically set credentials for the package facade.
     */
    protected function setPackageConfig()
    {
        if (!empty($this->config['api_key']) && !empty($this->config['secret_key'])) {
            config([
                'steadfast-courier.api_key' => $this->config['api_key'],
                'steadfast-courier.secret_key' => $this->config['secret_key'],
            ]);
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
        // Package does not have this method, keeping manual implementation
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

        $this->setPackageConfig();
        
        try {
            $result = SteadfastCourier::placeOrder($data);
            
            if ($result && isset($result['status']) && $result['status'] == 200) {
                return $result;
            }

            \Illuminate\Support\Facades\Log::error("Steadfast API Failed via Package. Response: " . json_encode($result));
            
            return array_merge(['error' => true], (array)$result);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Steadfast Package Error: " . $e->getMessage());
            return ['error' => true, 'message' => $e->getMessage()];
        }
    }

    public function getOrderStatus($consignmentId)
    {
        $this->setPackageConfig();
        try {
            $result = SteadfastCourier::checkDeliveryStatusByConsignmentId($consignmentId);
            return $result ?? ['error' => true, 'message' => 'Empty response from Steadfast'];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Steadfast Package Status Error: " . $e->getMessage());
            return null;
        }
    }

    public function getBalance()
    {
        $this->setPackageConfig();
        try {
            $result = SteadfastCourier::getCurrentBalance();
            return $result ?? ['error' => true, 'message' => 'Empty response from Steadfast'];
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Steadfast Package Balance Error: " . $e->getMessage());
            return null;
        }
    }

    public function getPoliceStations()
    {
        // Package does not have this method, keeping manual implementation
        $response = Http::withHeaders($this->getHeaders())->get($this->baseUrl . "/police_stations");
        return $response->successful() ? $response->json() : null;
    }
}
