<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:reports.view'),
            new Middleware('permission:products.edit', only: ['adjustStock']),
        ];
    }

    public function overview(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;

        // Total Online Sales
        $onlineSales = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total_amount');

        // Total POS Sales
        $posSales = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('total');

        $totalSales = $onlineSales + $posSales;

        // Expenses
        $expenses = \App\Models\VendorExpense::where('user_id', $vendorId)
            ->sum('amount');

        // Total Orders (Online + POS)
        $onlineOrdersCount = \App\Models\Order::where('user_id', $vendorId)->count();
        $posOrdersCount = \App\Models\PosSale::where('vendor_id', $vendorId)->count();
        $totalOrders = $onlineOrdersCount + $posOrdersCount;

        // Net Profit
        $netProfit = $totalSales - $expenses;
        
        return response()->json([
            'total_sales' => $totalSales,
            'expenses' => $expenses,
            'net_profit' => $netProfit,
            'total_orders' => $totalOrders,
            'trends' => [
                'sales' => 12, // Mocked for now
                'expenses' => 4,
                'profit' => 8.5,
            ],
            // Monthly Sales breakdown
            'monthly_sales' => $this->getMonthlySales($vendorId),
            // Expense Breakdown
            'expense_breakdown' => [
                ['label' => 'Logistics', 'val' => 40, 'color' => 'bg-indigo-500'],
                ['label' => 'Inventory', 'val' => 30, 'color' => 'bg-emerald-500'],
                ['label' => 'Marketing', 'val' => 20, 'color' => 'bg-amber-500'],
                ['label' => 'Staff', 'val' => 10, 'color' => 'bg-rose-500'],
            ]
        ]);
    }

    private function getMonthlySales($vendorId)
    {
        // Monthly Online Sales
        $onlineMonthly = \App\Models\Order::where('user_id', $vendorId)
            ->whereYear('created_at', date('Y'))
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->selectRaw('MONTH(created_at) as month, SUM(total_amount) as amount')
            ->groupBy('month')
            ->pluck('amount', 'month')
            ->toArray();

        // Monthly POS Sales
        $posMonthly = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->whereYear('created_at', date('Y'))
            ->where('status', 'paid')
            ->selectRaw('MONTH(created_at) as month, SUM(total) as amount')
            ->groupBy('month')
            ->pluck('amount', 'month')
            ->toArray();

        // Combine both
        $data = [];
        for ($i = 1; $i <= 12; $i++) {
            $online = $onlineMonthly[$i] ?? 0;
            $pos = $posMonthly[$i] ?? 0;
            $data[] = $online + $pos;
        }

        return $data;
    }

    public function sales(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        
        $onlineRevenue = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total_amount');

        $posRevenue = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('total');
            
        $totalRevenue = $onlineRevenue + $posRevenue;
            
        $onlineCount = \App\Models\Order::where('user_id', $vendorId)->count();
        $posCount = \App\Models\PosSale::where('vendor_id', $vendorId)->count();
        $totalOrders = $onlineCount + $posCount;

        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        $recentOnline = \App\Models\Order::with('customer')
            ->where('user_id', $vendorId)
            ->latest()
            ->take(5)
            ->get();

        return response()->json([
            'kpi' => [
                'total_revenue' => $totalRevenue,
                'total_orders' => $totalOrders,
                'avg_order_value' => $avgOrderValue
            ],
            'recent_transactions' => $recentOnline // Keeping online as "transactions" for now, can merge later
        ]);
    }

    public function returns(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $returns = \App\Models\Order::with('customer')
            ->where('user_id', $vendorId)
            ->where('status', 'returned')
            ->latest()
            ->paginate(10);
            
        return response()->json($returns);
    }

    public function cancels(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $cancels = \App\Models\Order::with('customer')
            ->where('user_id', $vendorId)
            ->where('status', 'cancelled')
            ->latest()
            ->paginate(10);
            
        return response()->json($cancels);
    }

    public function expenses(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $expenses = \App\Models\VendorExpense::where('user_id', $vendorId)
            ->latest()
            ->paginate(10);
            
        return response()->json([
            'total_expenses' => \App\Models\VendorExpense::where('user_id', $vendorId)->sum('amount'),
            'data' => $expenses
        ]);
    }

    public function coupons(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        // Assuming we look at discount amount in orders as coupon usage
        $couponsImpact = \App\Models\Order::where('user_id', $vendorId)
            ->sum('discount_amount');
            
        $activeCoupons = \App\Models\Coupon::where('user_id', $vendorId)
            ->where('status', 'active')
            ->count();

        return response()->json([
            'total_discount_given' => $couponsImpact,
            'active_coupons' => $activeCoupons
        ]);
    }

    public function productPerformance(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $products = \App\Models\Product::where('user_id', $vendorId)
            ->orderBy('sales', 'desc')
            ->take(10)
            ->get();
            
        return response()->json($products);
    }

    public function stock(Request $request)
    {
        $query = \App\Models\Product::query();

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('id', 'like', "%{$search}%");
            });
        }

        // Apply Filters
        if ($request->has('filter')) {
            switch ($request->filter) {
                case 'low_stock':
                    $query->where('stock_qty', '>', 0)->where('stock_qty', '<=', 5);
                    break;
                case 'out_of_stock':
                    $query->where('stock_qty', '<=', 0);
                    break;
            }
        }

        // Calculate KPIs for the (potentially filtered) set
        $allProducts = $query->get(['id', 'stock_qty', 'sale_price']);
        
        $inventoryValue = $allProducts->sum(function($product) {
            return (float)$product->stock_qty * (float)$product->sale_price;
        });

        $totalItems = $allProducts->sum('stock_qty');
        $lowStockCount = $allProducts->where('stock_qty', '>', 0)->where('stock_qty', '<=', 5)->count();
        $outOfStockCount = $allProducts->where('stock_qty', '<=', 0)->count();

        // Apply Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'valuation') {
            $query->orderByRaw('stock_qty * sale_price ' . $sortOrder);
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        // Paginate the results for the table
        $products = $query->select(['id', 'name', 'stock_qty', 'sale_price', 'thumbnail'])
            ->paginate($request->get('per_page', 10));

        return response()->json([
            'kpi' => [
                'inventory_value' => (float) $inventoryValue,
                'total_items' => (int) $totalItems,
                'low_stock_items' => (int) $lowStockCount,
                'out_of_stock_items' => (int) $outOfStockCount,
            ],
            'products' => $products
        ]);
    }

    public function adjustStock(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:add,subtract',
            'quantity' => 'required|integer|min:1',
            'note' => 'nullable|string|max:255',
        ]);

        // Automatically scoped via BelongsToVendor trait
        $product = \App\Models\Product::where('id', $request->product_id)->firstOrFail();

        $oldStock = $product->stock_qty;
        $adjustment = (int) $request->quantity;
        
        if ($request->type === 'subtract') {
            $adjustment = -$adjustment;
        }

        $newStock = $oldStock + $adjustment;
        
        // Ensure stock doesn't go below 0
        if ($newStock < 0) {
            $newStock = 0;
            $adjustment = -$oldStock;
        }

        $product->stock_qty = $newStock;
        $product->save();

        // Log the adjustment using HasStock trait
        $product->logStockChange($adjustment, 'adjustment', null, $request->note ?? 'Manual adjustment from stock report');

        return response()->json([
            'message' => 'Stock adjusted successfully',
            'product' => $product
        ]);
    }

    public function customers(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $totalCustomers = \App\Models\Customer::where('user_id', $vendorId)->count();
        
        $topCustomers = \App\Models\Customer::where('user_id', $vendorId)
            ->withCount('orders')
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        return response()->json([
            'total_customers' => $totalCustomers,
            'top_customers' => $topCustomers
        ]);
    }

    public function earnings(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        
        $onlineSales = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total_amount');

        $posSales = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('total');

        $totalSales = $onlineSales + $posSales;
            
        $vendorProfile = \App\Models\VendorProfile::where('user_id', $vendorId)->first();
        // Mock commission rate
        $commissionRate = 0.10; 
        $platformFee = $totalSales * $commissionRate;
        $netEarnings = $totalSales - $platformFee;

        return response()->json([
            'total_sales' => $totalSales,
            'platform_fee' => $platformFee,
            'net_earnings' => $netEarnings,
            'available_payout' => $vendorProfile ? $vendorProfile->balance : $netEarnings
        ]);
    }

    public function tax(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        
        $onlineSubtotal = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('subtotal');

        $posSubtotal = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('subtotal');
            
        $totalSubtotal = $onlineSubtotal + $posSubtotal;
            
        $estimatedTax = $totalSubtotal * 0.15;
        
        return response()->json([
            'total_tax_collected' => $estimatedTax,
            'tax_rate' => '15%'
        ]);
    }

    public function salesAnalytics(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        
        // Online Analytics
        $onlineAnalytics = \App\Models\Order::where('user_id', $vendorId)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue')
            ->groupBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();

        // POS Analytics
        $posAnalytics = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue')
            ->groupBy('date')
            ->get()
            ->pluck('revenue', 'date')
            ->toArray();
            
        // Merge and sort
        $merged = [];
        $today = now();
        for ($i = 6; $i >= 0; $i--) {
            $date = $today->copy()->subDays($i)->format('Y-m-d');
            $merged[] = [
                'date' => $date,
                'revenue' => ($onlineAnalytics[$date] ?? 0) + ($posAnalytics[$date] ?? 0)
            ];
        }
            
        return response()->json($merged);
    }
}
