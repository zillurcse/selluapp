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
        $shipping_type = get_setting('shipping_type', 'flat_rate');
        \Log::info("getShippingCost starting. Type: $shipping_type, City ID: " . ($shipping_info['city_id'] ?? 'N/A'));

        $admin_products = array();
        $seller_products = array();

        $cartItem = $carts[$index];
        $product = \App\Models\Product::find($cartItem['product_id']);

        if (!$product) {
            \Log::warning("Product not found for cart item ID: " . $cartItem['product_id']);
            return 0;
        }

        if ($product->digital == 1) {
            \Log::info("Product is digital, shipping cost 0.");
            return 0;
        }

        foreach ($carts as $key => $cart_item) {
            $item_product = \App\Models\Product::find($cart_item['product_id']);
            if (!$item_product) continue;

            if ($item_product->added_by == 'admin') {
                array_push($admin_products, $cart_item['product_id']);
            } else {
                $product_ids = $seller_products[$item_product->user_id] ?? [];
                array_push($product_ids, $cart_item['product_id']);
                $seller_products[$item_product->user_id] = $product_ids;
            }
        }

        if ($shipping_type == 'flat_rate') {
            $cost = (float)get_setting('flat_rate_shipping_cost', 0) / count($carts);
            \Log::info("Flat rate cost: $cost");
            return $cost;
        } elseif ($shipping_type == 'seller_wise_shipping') {
            if ($product->added_by == 'admin') {
                $cost = (float)get_setting('shipping_cost_admin', 0) / count($admin_products);
            } else {
                $vendor_shipping = get_setting('vendor_shipping_cost', 0); 
                $cost = (float)$vendor_shipping / count($seller_products[$product->user_id]);
            }
            \Log::info("Seller wise cost: $cost");
            return $cost;
        } elseif ($shipping_type == 'area_wise_shipping') {
            $city = \App\Models\City::where('id', $shipping_info['city_id'])->first();
            if ($city != null) {
                \Log::info("Area wise. City: {$city->name}, Cost: {$city->cost}");
                if ($product->added_by == 'admin') {
                    $cost = (float)$city->cost / max(1, count($admin_products));
                } else {
                    $cost = (float)$city->cost / max(1, count($seller_products[$product->user_id] ?? [1]));
                }
                return $cost;
            }
            \Log::info("Area wise. City not found.");
            return 0;
        } elseif ($shipping_type == 'courier_wise_shipping') {
            \Log::info("Courier wise checking...");
            $pathao_active = get_setting('pathao_courier_activation') == 1;
            if (!$pathao_active) {
                $pathao_settings = get_setting('pathao');
                $pathao_active = isset($pathao_settings['active']) && $pathao_settings['active'];
            }

            if ($pathao_active) {
                $city = \App\Models\City::find($shipping_info['city_id']);
                \Log::info("Pathao block. City: " . ($city ? $city->name : 'No'));
                if ($city && $city->pathao_city_id && $city->pathao_zone_id) {
                    $pathao = new \App\Services\Courier\PathaoService();
                    $weight = 0;
                    foreach ($carts as $item) {
                        $item_p = \App\Models\Product::find($item['product_id']);
                        $weight += (($item_p->weight ?? 0) * $item['quantity']);
                    }
                    $weight = $weight > 0 ? $weight : 0.5;

                    static $pathao_cost_cache = [];
                    $cache_id = $city->pathao_city_id . '_' . $city->pathao_zone_id . '_' . $weight;
                    
                    if (isset($pathao_cost_cache[$cache_id])) {
                        $cost = $pathao_cost_cache[$cache_id];
                        \Log::info("Pathao cost from cache: $cost");
                    } else {
                        \Log::info("Calling Pathao API for weight: $weight");
                        $cost = $pathao->calculateCost([
                            'store_id' => $pathao->getStoreId(),
                            'recipient_city' => $city->pathao_city_id,
                            'recipient_zone' => $city->pathao_zone_id,
                            'delivery_type' => 48,
                            'item_type' => 2,
                            'item_weight' => $weight,
                        ]);
                        $pathao_cost_cache[$cache_id] = $cost;
                        \Log::info("Pathao API returned: " . ($cost ?: 'NULL'));
                    }
                    return (float)($cost ?: 0) / count($carts);
                }
            }
            \Log::info("Courier wise returned 0.");
            return 0;
        } else {
            \Log::info("Default shipping cost returned: " . ($product->shipping_cost ?? 0));
            return (float)($product->shipping_cost ?? 0);
        }
    }
}
