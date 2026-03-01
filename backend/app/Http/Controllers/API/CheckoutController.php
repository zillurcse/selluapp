<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ShopSetting;
use App\Models\FraudCheck;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function getPaymentMethods(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
        ]);

        // Determine vendor from the first item
        $product = Product::findOrFail($validated['items'][0]['id']);
        $vendorId = $product->vendor_id;

        // 1. Load active payment methods defined by admin
        $adminMethods = \App\Models\PaymentMethod::where('is_active', true)->get();

        // 2. Load vendor's specific configurations
        $configResponse = ShopSetting::where('user_id', $vendorId)
            ->where('group', 'payment_gateways')
            ->get()
            ->pluck('value', 'key');

        $savedConfig = [];
        foreach ($configResponse as $key => $val) {
            $decoded = json_decode($val, true);
            $savedConfig[$key] = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
        }

        $adminSlugs = $adminMethods->pluck('slug')->toArray();
        $gateways = [];

        // Add admin methods that are activated by the vendor
        foreach ($adminMethods as $method) {
            $slug = $method->slug;
            if (isset($savedConfig[$slug]) && $savedConfig[$slug]['active']) {
                $gateways[] = [
                    'slug' => $slug,
                    'name' => $method->name,
                    'type' => 'admin',
                    'icon' => $method->icon,
                ];
            }
        }

        // Add vendor's custom methods
        foreach ($savedConfig as $key => $data) {
            if (!in_array($key, $adminSlugs) && isset($data['active']) && $data['active'] && isset($data['custom_meta'])) {
                $gateways[] = [
                    'slug' => $key,
                    'name' => $data['custom_meta']['name'] ?? 'Custom Method',
                    'type' => $data['custom_meta']['type'] ?? 'manual',
                    'icon' => $data['custom_meta']['icon'] ?? 'Banknote',
                    'instruction' => $data['config']['instruction'] ?? null, // Expose only safe configuration like instructions
                ];
            }
        }

        return response()->json([
            'success' => true,
            'gateways' => $gateways
        ]);
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'postal_code' => 'nullable|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        $city = \App\Models\City::findOrFail($validated['city_id']);

        DB::beginTransaction();
        try {
            // Group items by vendor
            $itemsByVendor = [];
            foreach ($validated['items'] as $item) {
                $product = Product::findOrFail($item['id']);
                $vendorId = $product->vendor_id;
                if (!isset($itemsByVendor[$vendorId])) {
                    $itemsByVendor[$vendorId] = [];
                }
                $itemsByVendor[$vendorId][] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                ];
            }

            $orders = [];
            $shippingApplied = false;
            foreach ($itemsByVendor as $vendorId => $vendorItems) {
                // Find or create customer for this vendor
                $customer = Customer::where('vendor_id', $vendorId)
                    ->where('email', $validated['email'])
                    ->first();

                if (!$customer) {
                    $customer = Customer::create([
                        'vendor_id' => $vendorId,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                    ]);
                }

                $subtotal = 0;
                foreach ($vendorItems as $vi) {
                    $price = $vi['product']->discount_price ?: $vi['product']->sale_price;
                    $subtotal += $price * $vi['quantity'];
                }

                $shippingCost = !$shippingApplied ? (float)$city->cost : 0.00; // Charge shipping only once
                $shippingApplied = true;
                
                $totalAmount = $subtotal + $shippingCost;

                // Check Spider Intelligence Settings
                $spiderIntelligenceSettings = ShopSetting::where('user_id', $vendorId)
                    ->where('group', 'spider_intelligence')
                    ->get()
                    ->pluck('value', 'key');

                $isSpiderActive = isset($spiderIntelligenceSettings['isActive']) && in_array($spiderIntelligenceSettings['isActive'], [true, 'true', '1'], true);
                $sensitivity = isset($spiderIntelligenceSettings['sensitivity']) ? (int)$spiderIntelligenceSettings['sensitivity'] : 65;
                
                $riskScore = 0;
                $requiresManualReview = false;
                $fraudFlags = [];

                if ($isSpiderActive) {
                    // Mock risk score calculation (random for now, could be based on order traits)
                    $riskScore = rand(0, 100);
                    $threshold = 100 - $sensitivity; // e.g. Sensitivity 65 => threshold 35
                    
                    if ($riskScore > $threshold) {
                        $requiresManualReview = true;
                        if ($riskScore > 80) {
                            $fraudFlags[] = 'High risk IP address detected.';
                        } else {
                            $fraudFlags[] = 'Unusual purchasing pattern.';
                        }
                    }
                }

                $order = Order::create([
                    'user_id' => $vendorId, // Vendor ID
                    'customer_id' => $customer->id,
                    'invoice_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'status' => 'pending',
                    'type' => 'normal',
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'unpaid',
                    'requires_manual_review' => $requiresManualReview,
                    'shipping_address' => json_encode([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'city' => $city->name,
                        'city_id' => $city->id,
                        'postal_code' => $validated['postal_code'] ?? '',
                    ]),
                ]);

                if ($requiresManualReview) {
                    FraudCheck::create([
                        'user_id' => $vendorId,
                        'order_id' => $order->id,
                        'risk_score' => $riskScore,
                        'flags' => $fraudFlags,
                        'status' => 'pending',
                    ]);
                }

                foreach ($vendorItems as $vi) {
                    $price = $vi['product']->discount_price ?: $vi['product']->sale_price;
                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $vi['product']->id,
                        'quantity' => $vi['quantity'],
                        'unit_price' => $price,
                        'total_price' => $price * $vi['quantity'],
                    ]);
                    
                    // Deduct stock
                    $vi['product']->decrement('stock_qty', $vi['quantity']);
                }

                $orders[] = $order;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Order placed successfully!',
                'orders' => $orders,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to place order: ' . $e->getMessage(),
            ], 500);
        }
    }
}
