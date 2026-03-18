<?php

use App\Models\BusinessSetting;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $vendorId = $user->vendor_id ?? $user->id;
            $setting = \App\Models\ShopSetting::where('user_id', $vendorId)
                ->where('key', $key)
                ->first();
            if ($setting) {
                // Return decoded JSON if it's a JSON string
                $val = $setting->value;
                $decoded = json_decode($val, true);
                return (json_last_error() == JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : $val;
            }
        }
        
        $setting = BusinessSetting::where('type', $key)->first();
        if ($setting) {
            $val = $setting->value;
            $decoded = json_decode($val, true);
            return (json_last_error() == JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : $val;
        }

        return $default;
    }
}

if (!function_exists('get_vendor_business_setting')) {
    function get_vendor_business_setting($vendorId, $type, $default = null)
    {
        $setting = \App\Models\BusinessSetting::where('vendor_id', $vendorId)
            ->where('type', $type)
            ->first();

        if ($setting) {
            $val = $setting->value;
            $decoded = json_decode($val, true);
            return (json_last_error() == JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : $val;
        }

        return $default;
    }
}

if (!function_exists('get_vendor_setting')) {
    function get_vendor_setting($vendorId, $key, $default = null)
    {
        $setting = \App\Models\ShopSetting::where('user_id', $vendorId)
            ->where('key', $key)
            ->first();
        if ($setting) {
            $val = $setting->value;
            $decoded = json_decode($val, true);
            return (json_last_error() == JSON_ERROR_NONE && (is_array($decoded) || is_object($decoded))) ? $decoded : $val;
        }

        return get_vendor_business_setting($vendorId, $key, $default);
    }
}

if (!function_exists('translate')) {
    function translate($text)
    {
        return $text;
    }
}

if (!function_exists('cart_product_price')) {
    function cart_product_price($cart_item, $product, $formatted = true, $with_tax = true)
    {
        $price = $product->discount_price ?: $product->sale_price;
        return $formatted ? format_price($price) : $price;
    }
}

if (!function_exists('format_price')) {
    function format_price($price)
    {
        return '৳' . number_format($price, 2);
    }
}

if (!function_exists('convert_price')) {
    function convert_price($price)
    {
        return $price;
    }
}

if (!function_exists('getShippingCost')) {
    function getShippingCost($carts, $index, $shipping_info = '')
    {
        $cartItem = $carts[$index];
        $product = \App\Models\Product::find($cartItem['product_id']);

        if (!$product) {
            return 0;
        }

        $vendorId = $product->vendor_id;
        $shipping_type = get_vendor_business_setting($vendorId, 'shipping_method');
        
        \Illuminate\Support\Facades\Log::info("getShippingCost debug: Product ID {$product->id}, Vendor ID: " . ($vendorId ?? 'NULL') . ", Type: " . ($shipping_type ?? 'NONE'));

        if (!$shipping_type) {
            return (float)($product->shipping_cost ?? 0);
        }

        // Prepare grouping by vendor
        $vendor_products_count = [];
        foreach ($carts as $item) {
            $p_id = $item['product_id'] ?? $item['id'] ?? null;
            $item_p = \App\Models\Product::find($p_id);
            if ($item_p) {
                $vId = $item_p->vendor_id;
                $vendor_products_count[$vId] = ($vendor_products_count[$vId] ?? 0) + 1;
            }
        }

        if (!empty($shipping_info['is_manual_location'])) {
            $baseCost = !empty($shipping_info['is_inside_dhaka']) ? 60 : 120;
            return (float)$baseCost / ($vendor_products_count[$vendorId] ?: 1);
        }

        if ($shipping_type == 'flat_rate') {
            $flat_rate_price = get_vendor_business_setting($vendorId, 'flat_rate_price', 0);
            $cost = (float)$flat_rate_price / ($vendor_products_count[$vendorId] ?: 1);
            return $cost;
        } elseif ($shipping_type == 'product_wise') {
            $default_cost = get_vendor_business_setting($vendorId, 'product_wise_shipping_cost', 0);
            return (float)($product->shipping_cost ?? $default_cost);
        } elseif ($shipping_type == 'area_wise_flat') {
            $city = \App\Models\City::find($shipping_info['city_id'] ?? null);
            if ($city) {
                $cost = (float)$city->cost / ($vendor_products_count[$vendorId] ?: 1);
                return $cost;
            }
            return (float)($product->shipping_cost ?? 0);
        } elseif ($shipping_type == 'carrier_wise') {
             $carrier = $shipping_info['carrier'] ?? 'personal';
             $carrier_prices = get_vendor_business_setting($vendorId, 'carrier_prices', []);
             $cost = (float)($carrier_prices[$carrier] ?? 0) / ($vendor_products_count[$vendorId] ?: 1);
             return $cost > 0 ? $cost : (float)($product->shipping_cost ?? 0);
        } elseif ($shipping_type == 'courier_wise') {
            $selected_courier = get_vendor_business_setting($vendorId, 'selected_courier', 'pathao');
            $courier_settings = get_vendor_business_setting($vendorId, 'courier_settings', []);
            
            if ($selected_courier == 'pathao') {
                $city = \App\Models\City::find($shipping_info['city_id'] ?? null);
                if ($city && $city->pathao_city_id && $city->pathao_zone_id) {
                    $pathao = new \App\Services\Courier\PathaoService($vendorId);
                    $weight = 0;
                    foreach ($carts as $item) {
                       $p_id = $item['product_id'] ?? $item['id'] ?? null;
                       $item_p = \App\Models\Product::find($p_id);
                       if ($item_p && $item_p->vendor_id == $vendorId) {
                           $weight += (($item_p->weight ?? 0) * $item['quantity']);
                       }
                    }
                    $weight = $weight > 0 ? $weight : 0.5;

                    try {
                        // Use vendor's store_id if available, otherwise sandbox check
                        $store_id = $courier_settings['pathao_store_id'] ?? null;
                        
                        // If sandbox is active and no store_id set, use user requested sandbox store id
                        $is_sandbox = get_vendor_setting($vendorId, 'pathao_sandbox', 0);
                        if ($is_sandbox && !$store_id) {
                            $store_id = '150036';
                        }
                        
                        $cost = $pathao->calculateCost([
                            'store_id' => $store_id ?: $pathao->getStoreId(),
                            'recipient_city' => $city->pathao_city_id,
                            'recipient_zone' => $city->pathao_zone_id,
                            'delivery_type' => 48,
                            'item_type' => 2,
                            'item_weight' => $weight,
                        ]);
                        return (float)($cost ?: (float)($product->shipping_cost ?? 0)) / ($vendor_products_count[$vendorId] ?: 1);
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Pathao calculation failed: " . $e->getMessage());
                    }
                }
            } elseif ($selected_courier == 'steadfast') {
                $city = \App\Models\City::find($shipping_info['city_id'] ?? null);
                $steadfast = new \App\Services\Courier\SteadfastService($vendorId);
                
                $weight = 0;
                foreach ($carts as $item) {
                   $p_id = $item['product_id'] ?? $item['id'] ?? null;
                   $item_p = \App\Models\Product::find($p_id);
                   if ($item_p && $item_p->vendor_id == $vendorId) {
                       $weight += (($item_p->weight ?? 0) * $item['quantity']);
                   }
                }
                $weight = $weight > 0 ? $weight : 0.5;

                $is_inside_dhaka = false;
                if ($city) {
                    $cityName = strtolower($city->name);
                    if (str_contains($cityName, 'dhaka')) {
                        $is_inside_dhaka = true;
                    }
                }

                try {
                    $cost = $steadfast->checkDeliveryCharge([
                        'weight' => $weight,
                        'address' => $is_inside_dhaka ? 'inside_dhaka' : 'outside_dhaka'
                    ]);
                    return (float)($cost ?: (float)($product->shipping_cost ?? 60)) / ($vendor_products_count[$vendorId] ?: 1);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("Steadfast calculation failed: " . $e->getMessage());
                }
            }
            return (float)($product->shipping_cost ?? 0);
        } else {
            return (float)($product->shipping_cost ?? 0);
        }
    }
}
