<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:orders.view', only: ['index', 'show']),
            new Middleware('permission:orders.edit', only: ['update']),
            new Middleware('permission:orders.delete', only: ['destroy']),
        ];
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $vendorId = auth()->id();
        $status = $request->status ?? 'All';
        $type = $request->type ?? 'All';
        $search = $request->search;
        $date = $request->date;

        // 1. Fetch POS Sales
        $posSales = collect();
        if ($type === 'All' || strtolower($type) === 'pos') {
            $posQuery = \App\Models\PosSale::with(['customer', 'items'])->where('vendor_id', $vendorId);
            
            if ($status !== 'All' && $status !== 'latest') {
                $posStatus = strtolower($status);
                if ($posStatus === 'delivered') $posStatus = 'paid';
                elseif ($posStatus === 'process') $posStatus = 'pending';
                
                if (in_array($posStatus, ['pending', 'paid', 'cancelled'])) {
                    $posQuery->where('status', $posStatus);
                } else {
                    $posQuery->whereRaw('1=0'); 
                }
            }

            if ($search) {
                $posQuery->where(function($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('phone', 'like', "%{$search}%")
                             ->orWhere('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            if ($date) {
                $posQuery->whereDate('created_at', $date);
            }

            $posSales = $posQuery->latest()->get();
        }

        // 2. Fetch Standard Orders
        $standardOrders = collect();
        if ($type === 'All' || strtolower($type) === 'normal' || strtolower($type) === 'pre-order') {
            $orderQuery = \App\Models\Order::with(['customer', 'items.product', 'fraudCheck'])->where('user_id', $vendorId);
            
            if ($status !== 'All' && $status !== 'latest') {
                $orderQuery->where('status', strtolower($status));
            }

            if (strtolower($type) !== 'all') {
                $orderQuery->where('type', strtolower($type));
            }

            if ($search) {
                $orderQuery->where(function($q) use ($search) {
                    $q->where('invoice_number', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('phone', 'like', "%{$search}%")
                             ->orWhere('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            if ($date) {
                $orderQuery->whereDate('created_at', $date);
            }

            $standardOrders = $orderQuery->latest()->get();
        }

        // 3. Map Data
        $mappedPos = $posSales->map(function ($sale) {
            $customerName = optional($sale->customer)->first_name ? trim($sale->customer->first_name . ' ' . $sale->customer->last_name) : 'Walk-in Customer';
            $words = explode(' ', $customerName);
            $initials = (count($words) > 1) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($customerName, 0, 2));

            return [
                'id' => $sale->id,
                'customer' => [
                    'name' => $customerName,
                    'initials' => $initials,
                    'phone' => optional($sale->customer)->phone ?? 'N/A',
                    'vip' => false,
                ],
                'invoice' => $sale->reference,
                'status' => $sale->status === 'paid' ? 'Delivered' : ucfirst($sale->status),
                'date' => $sale->created_at->format('d M, Y'),
                'created_at' => $sale->created_at,
                'time' => $sale->created_at->format('H:i'),
                'risk' => ['score' => 0, 'flags' => ['In-Store']],
                'products' => [
                    'count' => $sale->items->sum('qty'),
                    'skus' => $sale->items->count(),
                    'images' => ['https://placehold.co/100x100?text=POS'],
                ],
                'amount' => [
                    'total' => '৳' . number_format($sale->total, 2),
                    'discount' => $sale->discount_amount,
                    'method' => ucfirst(str_replace('_', ' ', $sale->payment_method)),
                    'paid' => $sale->status === 'paid',
                ],
                'type' => 'POS',
                'zone' => 'In-Store',
                'courier' => ['name' => '', 'tracking' => '']
            ];
        });

        $mappedOrders = $standardOrders->map(function ($order) {
            $images = []; $skus = 0; $itemCount = 0;
            foreach($order->items as $item) {
                $itemCount += $item->quantity; $skus++;
                if($item->product && $item->product->thumbnail) $images[] = asset('storage/' . $item->product->thumbnail);
            }
            if(empty($images)) $images[] = 'https://placehold.co/100x100?text=No+Image';

            $customerName = optional($order->customer)->first_name ? trim($order->customer->first_name . ' ' . $order->customer->last_name) : 'Guest User';
            $words = explode(' ', $customerName);
            $initials = (count($words) > 1) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($customerName, 0, 2));

            $trackingParts = explode(':', $order->tracking_number ?? '', 2);
            $courierName = count($trackingParts) == 2 ? $trackingParts[0] : null;
            $trackingCode = count($trackingParts) == 2 ? $trackingParts[1] : ($order->tracking_number ?? '');

            return [
                'id' => $order->id,
                'customer' => [
                    'name' => $customerName,
                    'initials' => $initials,
                    'phone' => optional($order->customer)->phone ?? 'N/A',
                    'vip' => false,
                ],
                'invoice' => $order->invoice_number,
                'status' => ucfirst($order->status),
                'date' => $order->created_at->format('d M, Y'),
                'created_at' => $order->created_at,
                'time' => $order->created_at->format('H:i'),
                'risk' => ['score' => $order->risk_score ?? 0, 'flags' => is_array($order->risk_flags) ? $order->risk_flags : []],
                'products' => ['count' => $itemCount, 'skus' => $skus, 'images' => $images],
                'amount' => [
                    'total' => '৳' . number_format(($order->subtotal + $order->shipping_cost) - $order->discount_amount, 2),
                    'discount' => $order->discount_amount,
                    'method' => ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')),
                    'paid' => $order->payment_status === 'paid',
                ],
                'type' => ucfirst(str_replace('_', ' ', $order->type)),
                'zone' => $order->delivery_zone ?? 'N/A',
                'courier' => ['name' => $courierName, 'tracking' => $trackingCode]
            ];
        });

        // 4. Merge and Paginate
        $all = $mappedPos->concat($mappedOrders)->sortByDesc('created_at');
        $perPage = 15;
        $page = $request->input('page', 1);
        $total = $all->count();
        $items = $all->forPage($page, $perPage)->values();

        // 5. Calculate Global Counts for Tabs
        $counts = [
            'latest' => \App\Models\Order::where('user_id', $vendorId)->count() + \App\Models\PosSale::where('vendor_id', $vendorId)->count(),
            'pending' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'pending')->count() + \App\Models\PosSale::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'approved' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'approved')->count(),
            'process' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'processing')->count() + \App\Models\PosSale::where('vendor_id', $vendorId)->where('status', 'pending')->count(),
            'courier' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'courier')->count(),
            'delivered' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'delivered')->count() + \App\Models\PosSale::where('vendor_id', $vendorId)->where('status', 'paid')->count(),
            'cancelled' => \App\Models\Order::where('user_id', $vendorId)->where('status', 'cancelled')->count() + \App\Models\PosSale::where('vendor_id', $vendorId)->where('status', 'cancelled')->count(),
        ];

        return response()->json([
            'data' => $items,
            'counts' => $counts,
            'meta' => [
                'current_page' => (int)$page,
                'last_page' => (int)ceil($total / $perPage),
                'total' => $total,
            ]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $id)
    {
        if ($request->filled('type') && strtolower($request->type) === 'pos') {
            $sale = \App\Models\PosSale::with(['customer', 'items'])->where('vendor_id', auth()->id())->findOrFail($id);
            return response()->json(['data' => $sale, 'type' => 'POS']);
        }

        $order = \App\Models\Order::with(['customer', 'items.product', 'fraudCheck'])->where('user_id', auth()->id())->findOrFail($id);
        return response()->json(['data' => $order, 'type' => 'Standard']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|string',
        ]);

        if ($request->filled('type') && strtolower($request->type) === 'pos') {
            $sale = \App\Models\PosSale::where('vendor_id', auth()->id())->findOrFail($id);
            
            // Map frontend statuses to POS statuses
            $status = strtolower($request->status);
            if ($status === 'delivered') $status = 'paid';
            if ($status === 'processing') $status = 'pending';
            
            $sale->update(['status' => $status]);
            return response()->json(['message' => 'POS Sale status updated successfully', 'data' => $sale]);
        }

        $order = \App\Models\Order::with('items.product')->where('user_id', auth()->id())->findOrFail($id);
        $oldStatus = $order->status;
        $newStatus = strtolower($request->status);

        DB::beginTransaction();
        try {
            $order->update(['status' => $newStatus]);

            // Deduct stock only when moving from pending to processing
            if ($oldStatus === 'pending' && $newStatus === 'processing') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrement('stock_qty', $item->quantity);
                    }
                }
                
                // --- Facebook CAPI Custom Events ---
                try {
                    $shippingInfo = json_decode($order->shipping_address, true);
                    $userData = [
                        'email' => $shippingInfo['email'] ?? null,
                        'phone' => $shippingInfo['phone'] ?? null,
                        'first_name' => $shippingInfo['first_name'] ?? null,
                        'last_name' => $shippingInfo['last_name'] ?? null,
                        'city' => $shippingInfo['city'] ?? null,
                        'user_id' => $order->customer_id,
                    ];
                    $customData = [
                        'currency' => 'BDT',
                        'value' => $order->total_amount,
                        'order_id' => $order->invoice_number,
                    ];
                    $fbCapi = new \App\Services\FacebookCapiService();
                    $fbCapi->sendEvent('StockConfirmed', $userData, $customData, (string)\Illuminate\Support\Str::uuid(), $order->user_id);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("CAPI StockConfirmed Error: " . $e->getMessage());
                }
            }

            // Refund logic
            if ($oldStatus !== 'refunded' && $newStatus === 'refunded') {
                try {
                    $shippingInfo = json_decode($order->shipping_address, true);
                    $userData = [
                        'email' => $shippingInfo['email'] ?? null,
                        'phone' => $shippingInfo['phone'] ?? null,
                        'first_name' => $shippingInfo['first_name'] ?? null,
                        'last_name' => $shippingInfo['last_name'] ?? null,
                        'city' => $shippingInfo['city'] ?? null,
                        'user_id' => $order->customer_id,
                    ];
                    $customData = [
                        'currency' => 'BDT',
                        'value' => $order->total_amount,
                        'order_id' => $order->invoice_number,
                    ];
                    $fbCapi = new \App\Services\FacebookCapiService();
                    $fbCapi->sendEvent('RefundIssued', $userData, $customData, (string)\Illuminate\Support\Str::uuid(), $order->user_id);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error("CAPI RefundIssued Error: " . $e->getMessage());
                }
            }

            DB::commit();
            return response()->json(['message' => 'Successfully updated order status to ' . $newStatus, 'data' => $order]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Failed to update order: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, string $id)
    {
        if ($request->filled('type') && strtolower($request->type) === 'pos') {
            $sale = \App\Models\PosSale::where('vendor_id', auth()->id())->findOrFail($id);
            $sale->items()->delete();
            $sale->delete();
            return response()->json(['message' => 'POS Sale deleted successfully']);
        }

        $order = \App\Models\Order::where('user_id', auth()->id())->findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return response()->json(['message' => 'Order deleted successfully']);
    }

    /**
     * Send order to a courier service.
     */
    public function syncCourier(Request $request, string $id)
    {
        $request->validate([
            'courier' => 'required|in:steadfast,pathao,redx',
        ]);

        $order = \App\Models\Order::with(['customer', 'items.product'])->where('user_id', auth()->id())->findOrFail($id);

        if ($order->tracking_number && strpos($order->tracking_number, ':') !== false) {
            return response()->json(['message' => 'Order already synced with a courier', 'type' => 'error'], 400);
        }

        $deliverySetting = \App\Models\ShopSetting::where('user_id', auth()->id())
            ->where('group', 'delivery')
            ->where('key', $request->courier)
            ->first();

        if (!$deliverySetting) {
            return response()->json(['message' => ucfirst($request->courier) . ' settings not found', 'type' => 'error'], 400);
        }

        $credentials = json_decode($deliverySetting->value, true);

        if (!$credentials || !isset($credentials['active']) || !$credentials['active']) {
            return response()->json(['message' => ucfirst($request->courier) . ' is not active', 'type' => 'error'], 400);
        }
        
        $customerName = optional($order->customer)->first_name ? trim($order->customer->first_name . ' ' . $order->customer->last_name) : (optional($order->customer)->name ?? 'Customer');

        if ($request->courier === 'steadfast') {
            $apiKey = $credentials['apiKey'] ?? '';
            $secretKey = $credentials['secretKey'] ?? '';

            if (!$apiKey || !$secretKey) {
                return response()->json(['message' => 'Steadfast credentials missing', 'type' => 'error'], 400);
            }

            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'Api-Key' => $apiKey,
                'Secret-Key' => $secretKey,
                'Content-Type' => 'application/json'
            ])->post('https://portal.packzy.com/api/v1/create_order', [
                'invoice' => $order->invoice_number,
                'recipient_name' => $customerName,
                'recipient_phone' => optional($order->customer)->phone ?? '01XXXXXXXXX',
                'recipient_address' => $order->shipping_address ?? 'N/A',
                'cod_amount' => $order->payment_status === 'paid' ? 0 : floatval($order->total_amount),
                'note' => $order->notes ?? '',
            ]);

            if ($response->successful() && isset($response['consignment']['tracking_code'])) {
                $order->tracking_number = 'Steadfast:' . $response['consignment']['tracking_code'];
                $order->status = 'courier';
                $order->save();

                return response()->json(['message' => 'Order synced with Steadfast successfully!', 'data' => $order]);
            }

            return response()->json([
                'message' => 'Failed to sync with Steadfast',
                'error' => $response->json()
            ], 400);
        }

        return response()->json(['message' => 'Courier integration not supported yet', 'type' => 'error'], 400);
    }
}
