<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\ShopSetting;
use App\Models\FraudCheck;
use App\Models\User;
use App\Services\Offers\OfferCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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

    public function getDiscount(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string',
            'use_loyalty_points' => 'nullable|boolean',
            'email' => 'nullable|email',
        ]);

        $cartItems = [];
        $vendorId = null;
        $subtotal = 0;

        foreach ($validated['items'] as $item) {
            $product = Product::find($item['id']);
            if (!$vendorId) $vendorId = $product->vendor_id;
            
            $price = (float)($product->discount_price ?: $product->sale_price);
            $subtotal += ($price * $item['quantity']);

            $cartItems[] = [
                'product_id' => $product->id,
                'price' => $price,
                'quantity' => $item['quantity'],
                'category_id' => $product->category_id
            ];
        }

        $service = new OfferCalculationService();
        $result = $service->calculateDiscounts($cartItems, $vendorId, $validated['coupon_code'] ?? null);

        // Loyalty Point Redemption Calculation
        $loyaltyDiscount = 0;
        $redeemablePoints = 0;
        $loyaltyMessage = '';
        
        if (($validated['use_loyalty_points'] ?? false) && $validated['email']) {
            if ($result['discount_total'] > 0) {
                $loyaltyMessage = 'Loyalty points cannot be used with other coupons or offers.';
            } else {
                $customer = Customer::where('vendor_id', $vendorId)
                    ->where('email', $validated['email'])
                    ->first();
                
                if ($customer && $customer->loyalty_points > 0) {
                    $loyaltySettings = ShopSetting::where('group', 'loyalty_program')
                        ->where('user_id', $vendorId)
                        ->get()
                        ->pluck('value', 'key');
                    
                    $isLoyaltyActive = isset($loyaltySettings['is_enabled']) && in_array($loyaltySettings['is_enabled'], [true, 'true', '1'], true);
                    
                    if ($isLoyaltyActive) {
                        // Calculate redeemable subtotal: Only products WITHOUT discount_price
                        $redeemableSubtotal = 0;
                        foreach ($validated['items'] as $item) {
                            $product = Product::find($item['id']);
                            if ($product && !$product->discount_price) {
                                $redeemableSubtotal += ($product->sale_price * $item['quantity']);
                            }
                        }

                        if ($redeemableSubtotal <= 0) {
                            $loyaltyMessage = 'Loyalty points can only be used on normal priced products.';
                        } else {
                            $pointValue = isset($loyaltySettings['point_value']) ? (float)$loyaltySettings['point_value'] : 0.1;
                            $maxPossibleDiscount = $redeemableSubtotal;
                            
                            $potentialLoyaltyDiscount = $customer->loyalty_points * $pointValue;
                            
                            if ($potentialLoyaltyDiscount > $maxPossibleDiscount) {
                                $loyaltyDiscount = $maxPossibleDiscount;
                                $redeemablePoints = ceil($maxPossibleDiscount / $pointValue);
                            } else {
                                $loyaltyDiscount = $potentialLoyaltyDiscount;
                                $redeemablePoints = $customer->loyalty_points;
                            }
                        }
                    }
                }
            }
        }

        $result['loyalty_discount'] = $loyaltyDiscount;
        $result['redeemable_points'] = $redeemablePoints;
        $result['loyalty_message'] = $loyaltyMessage;
        $result['discount_total'] += $loyaltyDiscount;

        return response()->json([
            'success' => true,
            'data' => $result
        ]);
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'phone' => 'required|string',
            'full_address' => 'required|string',
            'city_id' => 'required|exists:cities,id',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'carrier' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'use_loyalty_points' => 'nullable|boolean',
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
                // Handle guest account creation or lookup
                $user = User::where('email', $validated['email'])->first();
                
                if (!$user) {
                    // Create new User and Customer if not exists
                    $password = Str::random(12);
                    $user = User::create([
                        'name' => $validated['first_name'] . ' ' . $validated['last_name'],
                        'email' => $validated['email'],
                        'password' => Hash::make($password),
                        'vendor_id' => $vendorId,
                    ]);
                    $user->assignRole('customer');

                    $customer = Customer::create([
                        'vendor_id' => $vendorId,
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                    ]);
                } else {
                    // User exists, find or create associated customer record
                    $customer = Customer::where('vendor_id', $vendorId)
                        ->where('email', $validated['email'])
                        ->first();

                    if (!$customer) {
                        $customer = Customer::create([
                            'vendor_id' => $vendorId,
                            'first_name' => $validated['first_name'],
                            'last_name' => $validated['last_name'],
                            'email' => $validated['email'],
                            'phone' => $validated['phone'],
                        ]);
                    } else {
                        // Update existing customer phone if provided
                        $customer->update(['phone' => $validated['phone']]);
                    }
                }

                $subtotal = 0;
                $offerCartItems = [];
                foreach ($vendorItems as $vi) {
                    $price = (float)($vi['product']->discount_price ?: $vi['product']->sale_price);
                    $subtotal += ($price * $vi['quantity']);
                    $offerCartItems[] = [
                        'product_id' => $vi['product']->id,
                        'price' => $price,
                        'quantity' => $vi['quantity'],
                        'category_id' => $vi['product']->category_id
                    ];
                }

                // Apply Promotions
                $calculationService = new OfferCalculationService();
                $discountResult = $calculationService->calculateDiscounts($offerCartItems, $vendorId, $validated['coupon_code'] ?? null);
                $discountAmount = $discountResult['discount_total'];
                
                // Loyalty Point Redemption
                $loyaltyDiscount = 0;
                $pointsToRedeem = 0;
                if (($validated['use_loyalty_points'] ?? false) && $customer->loyalty_points > 0 && $discountAmount == 0) {
                    $loyaltySettings = \App\Models\ShopSetting::where('user_id', $vendorId)
                        ->where('group', 'loyalty_program')
                        ->get()
                        ->pluck('value', 'key');
                    
                    $isLoyaltyActive = isset($loyaltySettings['is_enabled']) && in_array($loyaltySettings['is_enabled'], [true, 'true', '1'], true);
                    
                    if ($isLoyaltyActive) {
                        // Calculate redeemable subtotal: Only products WITHOUT discount_price
                        $redeemableSubtotal = 0;
                        foreach ($vendorItems as $vi) {
                            if (!$vi['product']->discount_price) {
                                $redeemableSubtotal += ($vi['product']->sale_price * $vi['quantity']);
                            }
                        }

                        if ($redeemableSubtotal > 0) {
                            $pointValue = isset($loyaltySettings['point_value']) ? (float)$loyaltySettings['point_value'] : 0.1;
                            $maxDis = $redeemableSubtotal;
                            
                            $potentialDis = $customer->loyalty_points * $pointValue;
                            
                            if ($potentialDis > $maxDis) {
                                $loyaltyDiscount = $maxDis;
                                $pointsToRedeem = ceil($maxDis / $pointValue);
                            } else {
                                $loyaltyDiscount = $potentialDis;
                                $pointsToRedeem = $customer->loyalty_points;
                            }

                            if ($pointsToRedeem > 0) {
                                $customer->decrement('loyalty_points', $pointsToRedeem);
                                \App\Models\LoyaltyPointLog::create([
                                    'customer_id' => $customer->id,
                                    'vendor_id' => $vendorId,
                                    'points' => -$pointsToRedeem,
                                    'description' => "Points redeemed for order discount",
                                ]);
                                
                                $discountAmount += $loyaltyDiscount;
                                // Add to applied promotions for tracking
                                $appliedPromos = $discountResult['applied_offers'] ?? [];
                                $appliedPromos[] = [
                                    'name' => 'Loyalty Points Redemption',
                                    'discount' => $loyaltyDiscount,
                                    'points_redeemed' => $pointsToRedeem
                                ];
                                $discountResult['applied_offers'] = $appliedPromos;
                            }
                        }
                    }
                }
                
                // Prepare carts collection for getShippingCost helper
                $carts = collect();
                foreach ($validated['items'] as $item) {
                    $carts->push([
                        'product_id' => $item['id'],
                        'quantity' => $item['quantity'],
                    ]);
                }

                $shipping_info = [
                    'city_id' => $validated['city_id'],
                    'carrier' => $validated['carrier'] ?? 'personal'
                ];

                $total_shipping = 0;
                // Calculate shipping for only this vendor's items in the cart
                // Note: getShippingCost usually takes the full cart but we can pass current vendor's items if needed,
                // however most methods (flat_rate, area_wise) in helpers.php expect a relative index.
                // Let's find the indices for this vendor's items.
                foreach ($carts as $idx => $cItem) {
                    if (\App\Models\Product::find($cItem['product_id'])->vendor_id == $vendorId) {
                        $total_shipping += getShippingCost($carts, $idx, $shipping_info);
                    }
                }

                $shippingCost = !$shippingApplied ? (float)$total_shipping : 0.00; // Charge shipping only once per order group if needed, but here it's per vendor order. Actually, usually it's per vendor.
                $shippingApplied = true;
                
                $totalAmount = ($subtotal - $discountAmount) + (float)$shippingCost;

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
                    'discount_amount' => $discountAmount,
                    'applied_promotions' => $discountResult['applied_offers'] ?? [],
                    'shipping_cost' => $shippingCost,
                    'total_amount' => max(0, $totalAmount),
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'unpaid',
                    'requires_manual_review' => $requiresManualReview,
                    'shipping_address' => json_encode([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['full_address'],
                        'city' => $city->name,
                        'city_id' => $city->id,
                    ]),
                    'notes' => $validated['note'] ?? null,
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
                }

                // --- Loyalty Program Integration ---
                $loyaltySettings = \App\Models\ShopSetting::where('user_id', $vendorId)
                    ->where('group', 'loyalty_program')
                    ->get()
                    ->pluck('value', 'key');
                
                $isLoyaltyActive = isset($loyaltySettings['is_enabled']) && in_array($loyaltySettings['is_enabled'], [true, 'true', '1'], true);
                
                if ($isLoyaltyActive) {
                    $earningRate = isset($loyaltySettings['point_earning_rate']) ? (float)$loyaltySettings['point_earning_rate'] : 1; // points per 100 currency
                    $minOrderTotal = isset($loyaltySettings['min_order_total']) ? (float)$loyaltySettings['min_order_total'] : 0;

                    $eligibleAmount = $subtotal - $discountAmount;

                    if ($eligibleAmount >= $minOrderTotal) {
                        $earnedPoints = floor(($eligibleAmount / 100) * $earningRate);

                        if ($earnedPoints > 0) {
                            $customer->increment('loyalty_points', $earnedPoints);

                            \App\Models\LoyaltyPointLog::create([
                                'customer_id' => $customer->id,
                                'vendor_id' => $vendorId,
                                'order_id' => $order->id,
                                'points' => $earnedPoints,
                                'description' => "Points earned for order #" . $order->invoice_number,
                            ]);

                            // Attach earned points to order object for frontend visibility
                            $order->earned_points = $earnedPoints;
                        }
                    }
                }
                // -----------------------------------

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
