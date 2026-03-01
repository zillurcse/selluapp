<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BusinessSetting;
use App\Models\Order;
use App\Models\City;
use App\Services\Courier\PathaoService;
use App\Services\Courier\SteadfastService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class CourierController extends Controller
{
    public function index()
    {
        $settings = BusinessSetting::all();
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    public function update(Request $request)
    {
        foreach ($request->types as $key => $type) {
            $value = $request[$type];
            if (is_array($value)) {
                $value = json_encode($value);
            }

            BusinessSetting::updateOrCreate(
                ['type' => $type],
                ['value' => $value]
            );
        }

        Artisan::call('cache:clear');
        
        return response()->json([
            'status' => 'success',
            'message' => translate('Courier settings updated successfully')
        ]);
    }

    public function sendToPathao(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $pathao = new PathaoService();
        
        $shipping_address = is_string($order->shipping_address) ? json_decode($order->shipping_address) : (object)$order->shipping_address;
        
        // Calculate dynamic weight from order details
        $totalWeight = 0;
        foreach ($order->items as $item) {
            if ($item->product) {
                $totalWeight += ($item->product->weight * $item->quantity);
            }
        }
        $totalWeight = $totalWeight > 0 ? $totalWeight : 0.5;

        // Pathao expects 11 digit phone number starting with 01
        $phone = $request->recipient_phone ?? ($shipping_address->phone ?? '');
        $phone = preg_replace('/[^0-9]/', '', $phone); // Remove non-numeric
        if (strlen($phone) > 11 && str_starts_with($phone, '88')) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) == 10 && !str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        if (strlen($phone) != 11) {
            return response()->json([
                'status' => 'error',
                'message' => translate('Invalid phone number format for Pathao. Must be 11 digits.')
            ], 422);
        }

        $data = [
            'store_id' => (int)$pathao->getStoreId(),
            'merchant_order_id' => $order->invoice_number,
            'recipient_name' => $request->recipient_name ?? ($shipping_address->name ?? ''),
            'recipient_phone' => $phone,
            'recipient_address' => $request->recipient_address ?? ($shipping_address->address ?? ''),
            'recipient_city' => (int)$request->pathao_city_id,
            'recipient_zone' => (int)$request->pathao_zone_id,
            'recipient_area' => (int)$request->pathao_area_id,
            'delivery_type' => 48, // Standard
            'item_type' => 2, // Parcel
            'special_instruction' => $request->note ?? $order->notes,
            'item_quantity' => (int)$order->items->sum('quantity'),
            'item_weight' => (float)$totalWeight,
            'amount_to_collect' => $request->amount_to_collect ?? (($order->payment_status == 'unpaid') ? (float)$order->total_amount : 0),
            'item_description' => $request->item_description ?? ('Items from Order #' . $order->invoice_number)
        ];

        $result = $pathao->createOrder($data);
        if ($result && isset($result['consignment_id'])) {
            $order->courier_name = 'Pathao';
            $order->courier_order_id = $result['consignment_id'];
            $order->courier_status = $result['order_status'] ?? 'pending';
            $order->tracking_number = $result['consignment_id'];
            $order->status = 'courier';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => translate('Order successfully sent to Pathao. Consignment ID: ' . $result['consignment_id']),
                'data' => $result
            ]);
        } else {
            $msg = $result['message'] ?? translate('Failed to send order to Pathao.');
            if (isset($result['errors'])) {
                $msg .= ': ' . json_encode($result['errors']);
            }
            return response()->json([
                'status' => 'error',
                'message' => $msg
            ], 400);
        }
    }

    public function sendToSteadfast(Request $request)
    {
        $order = Order::findOrFail($request->order_id);
        $steadfast = new SteadfastService();
        
        $shipping_address = is_string($order->shipping_address) ? json_decode($order->shipping_address) : (object)$order->shipping_address;

        $phone = $request->recipient_phone ?? ($shipping_address->phone ?? '');
        $phone = preg_replace('/[^0-9]/', '', $phone); 
        if (strlen($phone) > 11 && str_starts_with($phone, '88')) {
            $phone = substr($phone, 2);
        }
        if (strlen($phone) == 10 && !str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        if (strlen($phone) != 11) {
            return response()->json([
                'status' => 'error',
                'message' => translate('Invalid phone number format for Steadfast. Must be 11 digits.')
            ], 422);
        }
        
        $data = [
            'invoice' => $order->invoice_number,
            'recipient_name' => $request->recipient_name ?? ($shipping_address->name ?? ''),
            'recipient_phone' => $phone,
            'recipient_address' => $request->recipient_address ?? ($shipping_address->address ?? ''),
            'cod_amount' => $request->cod_amount ?? ($order->payment_status == 'unpaid' ? (float)$order->total_amount : 0),
            'note' => $request->note ?? $order->notes,
            'item_description' => $request->item_description ?? ('Items from Order #' . $order->invoice_number)
        ];

        $result = $steadfast->createOrder($data);
        if ($result && isset($result['consignment']['consignment_id'])) {
            $order->courier_name = 'Steadfast';
            $order->courier_order_id = (string)$result['consignment']['consignment_id'];
            $order->courier_status = $result['consignment']['status'];
            $order->tracking_number = (string)$result['consignment']['consignment_id'];
            $order->status = 'courier';
            $order->save();

            return response()->json([
                'status' => 'success',
                'message' => translate('Order successfully sent to Steadfast. Consignment ID: ' . $order->courier_order_id),
                'data' => $result
            ]);
        } else {
            $msg = $result['message'] ?? translate('Failed to send order to Steadfast.');
            if (isset($result['errors'])) {
                $msg .= ': ' . json_encode($result['errors']);
            }
            return response()->json([
                'status' => 'error',
                'message' => $msg
            ], 400);
        }
    }

    public function updateStatus($id)
    {
        $order = Order::findOrFail($id);
        
        if ($order->courier_name == 'Pathao') {
            $pathao = new PathaoService();
            $result = $pathao->getOrderStatus($order->courier_order_id);
            if ($result && isset($result['order_status'])) {
                $order->courier_status = $result['order_status'];
                $order->save();
                return response()->json([
                    'status' => 'success',
                    'message' => translate('Pathao status updated to: ' . $order->courier_status)
                ]);
            }
        } elseif ($order->courier_name == 'Steadfast') {
            $steadfast = new SteadfastService();
            $result = $steadfast->getOrderStatus($order->courier_order_id);
            if ($result && isset($result['delivery_status'])) {
                $order->courier_status = $result['delivery_status'];
                $order->save();
                return response()->json([
                    'status' => 'success',
                    'message' => translate('Steadfast status updated to: ' . $order->courier_status)
                ]);
            }
        }
        
        return response()->json([
            'status' => 'error',
            'message' => translate('Failed to update status from courier.')
        ], 400);
    }

    public function steadfastWebhook(Request $request)
    {
        $configuredToken = get_setting('steadfast_webhook_auth_token');
        if ($configuredToken) {
            $bearerToken = $request->bearerToken();
            $queryToken = $request->input('token'); 
            
            if ($bearerToken !== $configuredToken && $queryToken !== $configuredToken) {
                return response()->json(['status' => 'error', 'message' => 'Unauthorized token'], 401);
            }
        }

        $consignmentId = $request->input('consignment_id');
        $status = $request->input('status');

        if ($consignmentId && $status) {
            $order = Order::where('courier_order_id', $consignmentId)->first();
            if ($order) {
                $order->courier_status = $status;
                $order->save();
            }
        }
        return response()->json(['status' => 'success']);
    }

    public function getPathaoCities()
    {
        $pathao = new PathaoService();
        $cities = $pathao->getCities();
        return response()->json($cities);
    }

    public function getPathaoZones($city_id)
    {
        $pathao = new PathaoService();
        $zones = $pathao->getZones($city_id);
        return response()->json($zones);
    }

    public function getPathaoAreas($zone_id)
    {
        $pathao = new PathaoService();
        $areas = $pathao->getAreas($zone_id);
        return response()->json($areas);
    }

    public function syncPathaoLocations()
    {
        set_time_limit(0); 
        ini_set('memory_limit', '1024M');

        $pathao = new PathaoService();
        $response = $pathao->getCities();

        if (isset($response['data']['data'])) {
            $pathaoCities = $response['data']['data'];
            
            // Map Pathao City to local State ID
            $states = \App\Models\State::all();
            $stateMap = [];
            foreach ($states as $st) {
                $name = strtolower(str_replace([' ', '-', '.'], '', $st->name));
                $stateMap[$name] = $st->id;
            }

            $syncedCount = 0;

            foreach ($pathaoCities as $pCity) {
                $pCityId = (int)$pCity['city_id'];
                $pCityName = trim($pCity['city_name']);
                
                $matchName = strtolower(str_replace([' ', '-', '.'], '', $pCityName));
                // Handle common differences
                if ($matchName == 'jashore') $matchName = 'jessor';
                if ($matchName == 'chittagong') $matchName = 'chattagam';
                if ($matchName == 'cumilla') $matchName = 'komilla';
                if ($matchName == 'gaibandha') $matchName = 'gaybanda';
                if ($matchName == 'bbaria') $matchName = 'brahmanbariya';
                if ($matchName == 'bagerhat') $matchName = 'bagarhat';

                $stateId = $stateMap[$matchName] ?? 1; 

                $zonesRes = $pathao->getZones($pCityId);
                if (!isset($zonesRes['data']['data'])) continue;

                foreach ($zonesRes['data']['data'] as $pZone) {
                    $pZoneId = (int)$pZone['zone_id'];
                    $pZoneName = trim($pZone['zone_name']);
                    
                    $areasRes = $pathao->getAreas($pZoneId);
                    $areas = isset($areasRes['data']['data']) ? $areasRes['data']['data'] : [];

                    if (empty($areas)) {
                        $this->upsertPathaoCityRecord($pCityName, $pZoneName, null, $pCityId, $pZoneId, null, $stateId);
                        $syncedCount++;
                    } else {
                        foreach ($areas as $pArea) {
                            $pAreaId = (int)$pArea['area_id'];
                            $pAreaName = trim($pArea['area_name']);
                            $this->upsertPathaoCityRecord($pCityName, $pZoneName, $pAreaName, $pCityId, $pZoneId, $pAreaId, $stateId);
                            $syncedCount++;
                        }
                    }
                }
            }

            return response()->json([
                'status' => 'success',
                'message' => "Sync complete. $syncedCount Pathao locations synced."
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => translate('Failed to fetch cities from Pathao.')
        ], 400);
    }

    private function upsertPathaoCityRecord($cityName, $zoneName, $areaName, $pCityId, $pZoneId, $pAreaId, $stateId)
    {
        $query = City::where('pathao_city_id', $pCityId)
                     ->where('pathao_zone_id', $pZoneId);
        
        if ($pAreaId) {
            $query->where('pathao_area_id', $pAreaId);
        } else {
            $query->whereNull('pathao_area_id');
        }

        $city = $query->first();

        if (!$city) {
            $city = new City();
            $city->cost = 0;
            $city->status = 1;
        }

        if ($areaName) {
            $city->name = "{$areaName}, {$zoneName}, {$cityName}";
        } else {
            $city->name = "{$zoneName}, {$cityName}";
        }

        $city->pathao_city_id = $pCityId;
        $city->pathao_zone_id = $pZoneId;
        $city->pathao_area_id = $pAreaId;
        $city->state_id = $stateId;
        $city->save();
    }
}
