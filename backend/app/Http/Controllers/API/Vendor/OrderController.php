<?php

namespace App\Http\Controllers\API\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

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
        // ---------------------------------------------------------
        // Handle POS Sales Request
        // ---------------------------------------------------------
        if ($request->filled('type') && strtolower($request->type) === 'pos') {
            $query = \App\Models\PosSale::with(['customer', 'items'])->where('vendor_id', auth()->id());

            if ($request->filled('status') && $request->status !== 'All' && $request->status !== 'latest') {
                $statusFilter = strtolower($request->status);
                if ($statusFilter === 'delivered') {
                    $statusFilter = 'paid';
                }
                $query->where('status', $statusFilter);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                      ->orWhereHas('customer', function($cq) use ($search) {
                          $cq->where('phone', 'like', "%{$search}%")
                             ->orWhere('first_name', 'like', "%{$search}%")
                             ->orWhere('last_name', 'like', "%{$search}%");
                      });
                });
            }

            if ($request->filled('date')) {
                $query->whereDate('created_at', $request->date);
            }

            $sales = $query->latest()->paginate(15);

            $mappedSales = $sales->through(function ($sale) {
                // Customer initials
                $customerName = optional($sale->customer)->first_name  
                    ? trim($sale->customer->first_name . ' ' . $sale->customer->last_name) 
                    : 'Walk-in Customer';
                
                $words = explode(' ', $customerName);
                $initials = (count($words) > 1) 
                    ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) 
                    : strtoupper(substr($customerName, 0, 2));

                $itemCount = $sale->items->sum('qty');
                $skus = $sale->items->count();

                return [
                    'id' => $sale->id,
                    'customer' => [
                        'name' => $customerName,
                        'initials' => $initials,
                        'phone' => optional($sale->customer)->phone ?? 'N/A',
                        'vip' => false,
                    ],
                    'invoice' => $sale->reference,
                    // Map POS status terminology to fit Order tab visually
                    'status' => $sale->status === 'paid' ? 'Delivered' : ucfirst($sale->status), 
                    'date' => $sale->created_at->format('d M, Y'),
                    'time' => $sale->created_at->format('H:i'),
                    'risk' => [
                        'score' => 0,
                        'flags' => ['In-Store'],
                    ],
                    'products' => [
                        'count' => $itemCount,
                        'skus' => $skus,
                        'images' => ['https://placehold.co/100x100?text=POS'], // Fallback for POS 
                    ],
                    'amount' => [
                        'total' => '৳' . number_format($sale->total, 2),
                        'method' => ucfirst(str_replace('_', ' ', $sale->payment_method)),
                        'paid' => $sale->status === 'paid',
                    ],
                    'type' => 'POS',
                    'zone' => 'In-Store',
                    'courier' => [
                        'name' => '', 
                        'tracking' => '',
                    ]
                ];
            });

            return response()->json([
                'data' => $mappedSales->items(),
                'meta' => [
                    'current_page' => $sales->currentPage(),
                    'last_page' => $sales->lastPage(),
                    'total' => $sales->total(),
                ]
            ]);
        }

        // ---------------------------------------------------------
        // Handle Standard Online Orders
        // ---------------------------------------------------------
        $query = \App\Models\Order::with(['customer', 'items.product', 'fraudCheck'])->where('user_id', auth()->id());

        // Simple filtering
        if ($request->filled('status') && $request->status !== 'All' && $request->status !== 'latest') {
            $query->where('status', strtolower($request->status));
        }
        
        if ($request->filled('type') && $request->type !== 'All') {
            $query->where('type', strtolower($request->type));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($cq) use ($search) {
                      $cq->where('phone', 'like', "%{$search}%")
                         ->orWhere('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        $orders = $query->latest()->paginate(15);

        // Map data specifically for the frontend table structure
        $mappedOrders = $orders->through(function ($order) {
            
            // Extract item images
            $images = [];
            $skus = 0;
            $itemCount = 0;
            
            foreach($order->items as $item) {
                $itemCount += $item->quantity;
                $skus++;
                if($item->product && $item->product->thumbnail) {
                    $images[] = asset('storage/' . $item->product->thumbnail);
                }
            }
            if(empty($images)) {
                $images[] = 'https://placehold.co/100x100?text=No+Image';
            }

            // Customer initials
            $customerName = optional($order->customer)->name ?? 'Guest User';
            $words = explode(' ', $customerName);
            $initials = (count($words) > 1) ? strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1)) : strtoupper(substr($customerName, 0, 2));

            // Risk Flags mapping
            $flags = is_array($order->risk_flags) ? $order->risk_flags : [];

            return [
                'id' => $order->id,
                'customer' => [
                    'name' => $customerName,
                    'initials' => $initials,
                    'phone' => optional($order->customer)->phone ?? 'N/A',
                    'vip' => false, // Can be based on customer purchase history later
                ],
                'invoice' => $order->invoice_number,
                'status' => ucfirst($order->status),
                'date' => $order->created_at->format('d M, Y'),
                'time' => $order->created_at->format('H:i'),
                'risk' => [
                    'score' => $order->risk_score ?? 0,
                    'flags' => $flags,
                ],
                'products' => [
                    'count' => $itemCount,
                    'skus' => $skus,
                    'images' => $images,
                ],
                'amount' => [
                    'total' => '৳' . number_format($order->total_amount, 2),
                    'method' => ucfirst(str_replace('_', ' ', $order->payment_method ?? 'N/A')),
                    'paid' => $order->payment_status === 'paid',
                ],
                'type' => ucfirst(str_replace('_', ' ', $order->type)),
                'zone' => $order->delivery_zone ?? 'N/A',
                'courier' => [
                    'name' => 'N/A', // Update when courier relation exists
                    'tracking' => $order->tracking_number ?? '',
                ]
            ];
        });

        return response()->json([
            'data' => $mappedOrders->items(),
            'meta' => [
                'current_page' => $orders->currentPage(),
                'last_page' => $orders->lastPage(),
                'total' => $orders->total(),
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

        $order = \App\Models\Order::where('user_id', auth()->id())->findOrFail($id);
        $order->update(['status' => strtolower($request->status)]);

        return response()->json(['message' => 'Order status updated successfully', 'data' => $order]);
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
}
