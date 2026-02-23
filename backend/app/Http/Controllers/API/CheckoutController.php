<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'address' => 'required|string',
            'city' => 'required|string',
            'postal_code' => 'required|string',
            'payment_method' => 'required|string',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

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

                $shippingCost = !$shippingApplied ? 15.00 : 0.00; // Charge shipping only once
                $shippingApplied = true;
                
                $totalAmount = $subtotal + $shippingCost;

                $order = Order::create([
                    'user_id' => $vendorId, // Vendor ID
                    'customer_id' => $customer->id,
                    'invoice_number' => 'ORD-' . strtoupper(Str::random(10)),
                    'status' => 'pending',
                    'type' => 'online',
                    'subtotal' => $subtotal,
                    'shipping_cost' => $shippingCost,
                    'total_amount' => $totalAmount,
                    'payment_method' => $validated['payment_method'],
                    'payment_status' => 'pending',
                    'shipping_address' => json_encode([
                        'first_name' => $validated['first_name'],
                        'last_name' => $validated['last_name'],
                        'email' => $validated['email'],
                        'address' => $validated['address'],
                        'city' => $validated['city'],
                        'postal_code' => $validated['postal_code'],
                    ]),
                ]);

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
