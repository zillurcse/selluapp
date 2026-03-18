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
use App\Services\SmsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

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
                $instruction = null;
                $vConfig = $savedConfig[$slug]['config'] ?? [];

                if ($slug === 'bkash' && isset($vConfig['number'])) {
                    $instruction = "Please send money to bKash " . ($vConfig['type'] ?? 'Personal') . " number: " . $vConfig['number'];
                } elseif ($slug === 'nagad' && isset($vConfig['number'])) {
                    $instruction = "Please send money to Nagad " . ($vConfig['type'] ?? 'Personal') . " number: " . $vConfig['number'];
                } elseif ($slug === 'rocket' && isset($vConfig['number'])) {
                    $instruction = "Please send money to Rocket " . ($vConfig['type'] ?? 'Personal') . " number: " . $vConfig['number'];
                } elseif ($slug === 'manual-bank' && isset($vConfig['account_number'])) {
                    $instruction = "Bank: " . ($vConfig['bank_name'] ?? 'N/A') . "\n" .
                                   "Account Name: " . ($vConfig['account_name'] ?? 'N/A') . "\n" .
                                   "Account Number: " . $vConfig['account_number'] . "\n" .
                                   "Branch: " . ($vConfig['branch_name'] ?? 'N/A') . "\n" .
                                   ($vConfig['instruction'] ?? '');
                }

                $gateways[] = [
                    'slug' => $slug,
                    'name' => $method->name,
                    'type' => 'admin',
                    'icon' => $method->icon,
                    'instruction' => $instruction,
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
            'city_id' => 'required_without:is_manual_location|nullable|exists:cities,id',
            'is_manual_location' => 'nullable|boolean',
            'state_name' => 'required_if:is_manual_location,true|nullable|string',
            'city_name' => 'required_if:is_manual_location,true|nullable|string',
            'is_inside_dhaka' => 'nullable|boolean',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'note' => 'nullable|string',
            'carrier' => 'nullable|string',
            'coupon_code' => 'nullable|string',
            'use_loyalty_points' => 'nullable|boolean',
        ]);

        $city = empty($validated['is_manual_location']) ? \App\Models\City::findOrFail($validated['city_id']) : null;
        $ip = $request->ip();

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
            $requiresPhoneVerification = false;
            $verificationOrderId = null;
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
                    'city_id' => $validated['city_id'] ?? null,
                    'is_manual_location' => !empty($validated['is_manual_location']),
                    'is_inside_dhaka' => $validated['is_inside_dhaka'] ?? false,
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

                // --- Fraud Detection Logic ---
                $fraudSettings = ShopSetting::where('user_id', $vendorId)
                    ->where('group', 'fraud_check')
                    ->get()
                    ->pluck('value', 'key');

                $blacklist = isset($fraudSettings['blacklist']) ? (is_array($fraudSettings['blacklist']) ? $fraudSettings['blacklist'] : json_decode($fraudSettings['blacklist'], true)) : [];

                // 1. Manual Blacklist Check
                if (in_array($ip, $blacklist) || in_array($validated['phone'], $blacklist)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Your access has been restricted due to suspicious activity.',
                    ], 403);
                }

                $riskScore = 0;
                $fraudFlags = [];

                // 2. IP Rate Limiting Rule
                $ipRule = isset($fraudSettings['ip_rate_limiting']) ? (is_array($fraudSettings['ip_rate_limiting']) ? $fraudSettings['ip_rate_limiting'] : json_decode($fraudSettings['ip_rate_limiting'], true)) : null;
                if ($ipRule && ($ipRule['enabled'] ?? false)) {
                    $recentOrdersCount = Order::where('user_id', $vendorId)
                        ->where(function($q) use ($ip) {
                            $q->where('shipping_address', 'like', "%{$ip}%");
                        })
                        ->where('created_at', '>=', now()->subHour())
                        ->count();

                    if ($recentOrdersCount >= 3) {
                        $riskScore += 40;
                        $fraudFlags[] = 'Multiple orders from same IP in a short period.';
                    }
                }

                // 3. Behavior Analysis Rule
                $behaviorRule = isset($fraudSettings['behavior_analysis']) ? (is_array($fraudSettings['behavior_analysis']) ? $fraudSettings['behavior_analysis'] : json_decode($fraudSettings['behavior_analysis'], true)) : null;
                if ($behaviorRule && ($behaviorRule['enabled'] ?? false)) {
                     // Check for repeated checkout attempts with same phone/email in last 10 mins
                     $attempts = Order::where('user_id', $vendorId)
                        ->where(function($q) use ($validated) {
                            $q->where('email', $validated['email'])
                              ->orWhere('phone', $validated['phone']);
                        })
                        ->where('created_at', '>=', now()->subMinutes(10))
                        ->count();

                    if ($attempts >= 2) {
                        $riskScore += 30;
                        $fraudFlags[] = 'Repeated checkout attempts detected.';
                    }
                }

                // Determine if manual review or verification is needed
                $requiresManualReview = $riskScore >= 40;
                $vendorRequiresVerification = false;

                $phoneRule = isset($fraudSettings['phone_verification']) ? (is_array($fraudSettings['phone_verification']) ? $fraudSettings['phone_verification'] : json_decode($fraudSettings['phone_verification'], true)) : null;

                if ($phoneRule && ($phoneRule['enabled'] ?? false) && $riskScore >= 30) {
                    $vendorRequiresVerification = true;
                }

                // Check if SMS gateway is configured and OTP template is active
                $smsSetting = ShopSetting::where('user_id', $vendorId)
                    ->where('group', 'third_party_apis')
                    ->where('key', 'sms')
                    ->first();
                $smsConfig = $smsSetting ? json_decode($smsSetting->value, true) : null;
                $hasActiveSms = $smsConfig && !empty($smsConfig['provider']);

                $otpTemplate = \App\Models\SmsTemplate::where('user_id', $vendorId)
                    ->where('type', 'otp')
                    ->where('is_active', true)
                    ->first();


                if ($hasActiveSms && $otpTemplate) {
                    $vendorRequiresVerification = true;
                }

                $order = Order::create([
                    'user_id' => $vendorId, // Vendor ID
                    'customer_id' => $customer->id,
                    'invoice_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'status' => $vendorRequiresVerification ? 'pending_verification' : 'pending',
                    'type' => 'normal',
                    'subtotal' => $subtotal,
                    'discount_amount' => $discountAmount,
                    'applied_promotions' => $discountResult['applied_offers'] ?? [],
                    'shipping_cost' => $shippingCost,
                    'total_amount' => max(0, $totalAmount),
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'unpaid',
                    'requires_manual_review' => $requiresManualReview,
                    'risk_score' => $riskScore,
                    'risk_flags' => $fraudFlags,
                    'shipping_address' => json_encode([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                        'phone' => $validated['phone'],
                        'address' => $validated['full_address'],
                        'city' => !empty($validated['is_manual_location']) ? $validated['city_name'] : $city->name,
                        'city_id' => !empty($validated['is_manual_location']) ? null : $city->id,
                        'state' => !empty($validated['is_manual_location']) ? $validated['state_name'] : null,
                        'ip' => $ip
                    ]),
                    'notes' => $validated['note'] ?? null,
                ]);

                if ($vendorRequiresVerification) {
                    $otp = (string)rand(100000, 999999);
                    $order->update([
                        'otp_code' => $otp,
                        'otp_expires_at' => now()->addMinutes(10)
                    ]);

                    // Send SMS
                    try {
                        \Illuminate\Support\Facades\Log::info("Attempting to send OTP SMS to {$validated['phone']} for Vendor: {$vendorId}");
                        $smsService = new SmsService($vendorId);

                        $shopName = ShopSetting::where('user_id', $vendorId)->where('key', 'shop_name')->first()?->value ?? 'Our Shop';

                        $sent = $smsService->send($validated['phone'], 'otp', [
                            'otp' => $otp,
                            'shop_name' => $shopName
                        ]);
                        \Illuminate\Support\Facades\Log::info("OTP SMS send status: " . ($sent ? 'Success' : 'Failed'));
                    } catch (\Exception $e) {
                        \Illuminate\Support\Facades\Log::error("Failed to send OTP SMS: " . $e->getMessage());
                    }

                    // For response logic outside loop
                    $requiresPhoneVerification = true;
                    $verificationOrderId = $order->id;
                }

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

                // --- Order notification for Vendor ---
                if (!$requiresPhoneVerification) {
                    \App\Models\Notification::create([
                        'user_id' => $vendorId,
                        'type'    => 'new_order',
                        'title'   => 'New Order Received',
                        'message' => 'Order ' . $order->invoice_number . ' has been placed for ৳' . number_format($order->total_amount, 2) . '.',
                        'data'    => [
                            'order_id'       => $order->id,
                            'invoice_number' => $order->invoice_number,
                            'link'           => '/vendor/orders',
                        ],
                    ]);

                    // --- Facebook CAPI Custom Events ---
                    try {
                        $userData = [
                            'email' => $validated['email'] ?? null,
                            'phone' => $validated['phone'] ?? null,
                            'first_name' => $validated['first_name'] ?? null,
                            'last_name' => $validated['last_name'] ?? null,
                            'city' => $city->name ?? null,
                            'ip' => $ip,
                            'user_agent' => $request->userAgent(),
                            'user_id' => $customer->id ?? null,
                        ];

                        $customData = [
                            'currency' => 'BDT',
                            'value' => max(0, $totalAmount),
                            'order_id' => $order->invoice_number,
                            'content_ids' => array_map(function($v) { return (string)$v['product']->id; }, $vendorItems),
                        ];

                        $fbCapi = new \App\Services\FacebookCapiService();
                        $fbCapi->sendEvent('OrderCreated', $userData, $customData, (string)\Illuminate\Support\Str::uuid(), $vendorId);

                        if ($discountAmount > 0) {
                             $fbCapi->sendEvent('CouponApplied', $userData, array_merge($customData, ['custom_params' => ['coupon' => $validated['coupon_code'] ?? 'offer']]), (string)\Illuminate\Support\Str::uuid(), $vendorId);
                        }
                    } catch (\Exception $e) {
                         \Illuminate\Support\Facades\Log::error("CAPI Error: " . $e->getMessage());
                    }
                }
                // ------------------------------------

                $orders[] = $order;
            }

            DB::commit();

            if (isset($requiresPhoneVerification) && $requiresPhoneVerification && count($orders) === 1) {
                return response()->json([
                    'success' => true,
                    'message' => 'Verification required. Please enter the OTP sent to your phone.',
                    'requires_verification' => true,
                    'order_id' => $verificationOrderId ?? $orders[0]->id,
                ], 202);
            }

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

    /**
     * Verify OTP for an order
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'otp' => 'required|string|size:6',
        ]);

        $order = Order::findOrFail($request->order_id);

        \Illuminate\Support\Facades\Log::info("Order found for OTP verification. Order ID: {$order->id}, Current OTP: {$order->otp_code}, Provided OTP: {$request->otp}");

        if ($order->otp_code != $request->otp) {
            \Illuminate\Support\Facades\Log::warning("Invalid OTP provided for order ID: {$order->id}. Provided: {$request->otp}, Expected: {$order->otp_code}");
            return response()->json([
                'success' => false,
                'message' => 'Invalid OTP code.',
            ], 422);
        }

        \Illuminate\Support\Facades\Log::info("OTP matched for order ID: {$order->id}. Checking expiration.");

        if ($order->otp_expires_at && now()->gt($order->otp_expires_at)) {
            \Illuminate\Support\Facades\Log::warning("OTP expired for order ID: {$order->id}. Expiration: {$order->otp_expires_at}");
            return response()->json([
                'success' => false,
                'message' => 'OTP has expired. Please request a new one.',
            ], 422);
        }

        $order->update([
            'status' => 'pending',
            'otp_code' => null,
            'otp_expires_at' => null,
            'requires_manual_review' => false // Auto-verify if OTP is correct
        ]);

        // Send notification to vendor now that it's verified
        \App\Models\Notification::create([
            'user_id' => $order->user_id,
            'type'    => 'new_order',
            'title'   => 'New Order Received (Verified)',
            'message' => 'Order ' . $order->invoice_number . ' has been verified and placed for ৳' . number_format($order->total_amount, 2) . '.',
            'data'    => [
                'order_id'       => $order->id,
                'invoice_number' => $order->invoice_number,
                'link'           => '/vendor/orders',
            ],
        ]);

        // --- Facebook CAPI Custom Events ---
        try {
            $shippingInfo = json_decode($order->shipping_address, true);
            $userData = [
                'email' => $shippingInfo['email'] ?? null,
                'phone' => $shippingInfo['phone'] ?? null,
                'first_name' => $shippingInfo['first_name'] ?? null,
                'last_name' => $shippingInfo['last_name'] ?? null,
                'city' => $shippingInfo['city'] ?? null,
                'ip' => $shippingInfo['ip'] ?? null,
                'user_agent' => $request->userAgent(),
                'user_id' => $order->customer_id,
            ];

            $customData = [
                'currency' => 'BDT',
                'value' => $order->total_amount,
                'order_id' => $order->invoice_number,
            ];

            $fbCapi = new \App\Services\FacebookCapiService();
            $fbCapi->sendEvent('OrderCreated', $userData, $customData, (string)\Illuminate\Support\Str::uuid(), $order->user_id);

            if ($order->discount_amount > 0) {
                 $coupon = !empty($order->applied_promotions) ? 'offer' : 'loyalty';
                 $fbCapi->sendEvent('CouponApplied', $userData, array_merge($customData, ['custom_params' => ['coupon' => $coupon]]), (string)\Illuminate\Support\Str::uuid(), $order->user_id);
            }
        } catch (\Exception $e) {
             \Illuminate\Support\Facades\Log::error("CAPI Error: " . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Order verified successfully!',
            'invoice_number' => $order->invoice_number,
        ]);
    }

    /**
     * Resend OTP for an order
     */
    public function resendOtp(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->status !== 'pending_verification') {
            return response()->json([
                'success' => false,
                'message' => 'Order is not in verification state.',
            ], 422);
        }

        $otp = (string)rand(100000, 999999);
        $order->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10)
        ]);

        $shippingInfo = json_decode($order->shipping_address, true);
        $phone = $shippingInfo['phone'] ?? null;

        if (!$phone) {
             return response()->json([
                'success' => false,
                'message' => 'Customer phone number not found.',
            ], 422);
        }

        try {
            \Illuminate\Support\Facades\Log::info("Resending OTP SMS to {$phone} for Vendor: {$order->user_id}");
            $smsService = new SmsService($order->user_id);
            $sent = $smsService->send($phone, 'otp', ['otp' => $otp]);

            if ($sent) {
                return response()->json([
                    'success' => true,
                    'message' => 'OTP has been resent.',
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to send SMS. Please check provider settings.',
                ], 500);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to resend OTP SMS: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
            ], 500);
        }
    }
}
