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
        $period   = $request->get('period', 'monthly'); // weekly | monthly | yearly

        // ── Date range & grouping logic ──────────────────────────────────────
        switch ($period) {
            case 'weekly':
                $startDate  = now()->subDays(6)->startOfDay();
                $groupFormat = 'DATE(created_at)';
                $labels      = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
                $labelFormat = fn($k) => $k; // already Y-m-d
                break;
            case 'yearly':
                $startDate   = now()->startOfYear()->subYears(4);
                $groupFormat  = 'YEAR(created_at)';
                $labels       = collect(range(now()->year - 4, now()->year));
                $labelFormat  = fn($k) => (string)$k;
                break;
            default: // monthly
                $startDate   = now()->startOfYear();
                $groupFormat  = 'MONTH(created_at)';
                $labels       = collect(range(1, 12));
                $labelFormat  = fn($k) => (string)$k;
        }

        // ── KPI: all-time totals ─────────────────────────────────────────────
        $onlineRevenue = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->sum('total_amount');

        $posRevenue = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->sum('total');

        $totalRevenue = $onlineRevenue + $posRevenue;

        $onlineCount = \App\Models\Order::where('user_id', $vendorId)->count();
        $posCount    = \App\Models\PosSale::where('vendor_id', $vendorId)->count();
        $totalOrders = $onlineCount + $posCount;
        $avgOrderValue = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        // ── Chart: period-scoped revenue ─────────────────────────────────────
        $onlineChart = \App\Models\Order::where('user_id', $vendorId)
            ->whereNotIn('status', ['cancelled', 'returned'])
            ->where('created_at', '>=', $startDate)
            ->selectRaw("{$groupFormat} as grp, SUM(total_amount) as amount")
            ->groupBy('grp')
            ->pluck('amount', 'grp')
            ->toArray();

        $posChart = \App\Models\PosSale::where('vendor_id', $vendorId)
            ->where('status', 'paid')
            ->where('created_at', '>=', $startDate)
            ->selectRaw("{$groupFormat} as grp, SUM(total) as amount")
            ->groupBy('grp')
            ->pluck('amount', 'grp')
            ->toArray();

        $chartData = $labels->map(function ($label) use ($onlineChart, $posChart, $labelFormat) {
            $key = $labelFormat($label);
            return [
                'label'  => $label,
                'amount' => ((float)($onlineChart[$key] ?? 0)) + ((float)($posChart[$key] ?? 0)),
            ];
        })->values()->toArray();

        // ── Top Products (by units sold via order items) ─────────────────────
        $topProducts = \App\Models\OrderItem::whereHas('order', function ($q) use ($vendorId) {
                $q->where('user_id', $vendorId)
                  ->whereNotIn('status', ['cancelled', 'returned']);
            })
            ->selectRaw('product_id, SUM(quantity) as total_sold, SUM(total_price) as total_revenue')
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->take(5)
            ->get()
            ->map(function ($item) {
                $product = \App\Models\Product::find($item->product_id, ['id', 'name', 'thumbnail']);
                return [
                    'id'            => $item->product_id,
                    'name'          => $product?->name ?? 'Unknown',
                    'thumbnail'     => $product?->thumbnail,
                    'total_sold'    => (int)$item->total_sold,
                    'total_revenue' => (float)$item->total_revenue,
                ];
            });

        // ── Recent Transactions ──────────────────────────────────────────────
        $recentOnline = \App\Models\Order::with('customer')
            ->where('user_id', $vendorId)
            ->latest()
            ->take(10)
            ->get();

        return response()->json([
            'kpi' => [
                'total_revenue'   => $totalRevenue,
                'total_orders'    => $totalOrders,
                'avg_order_value' => $avgOrderValue,
            ],
            'chart'               => $chartData,
            'top_products'        => $topProducts,
            'recent_transactions' => $recentOnline,
        ]);
    }

    public function returns(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $period   = $request->get('period', 'monthly');
        $page     = $request->get('page', 1);
        $perPage  = $request->get('per_page', 10);

        // ── Date range & grouping ─────────────────────────────────────────────
        switch ($period) {
            case 'weekly':
                $startDate   = now()->subDays(6)->startOfDay();
                $groupFormat = 'DATE(created_at)';
                $labels      = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
                $labelFormat = fn($k) => $k;
                break;
            case 'yearly':
                $startDate   = now()->startOfYear()->subYears(4);
                $groupFormat = 'YEAR(created_at)';
                $labels      = collect(range(now()->year - 4, now()->year));
                $labelFormat = fn($k) => (string)$k;
                break;
            default: // monthly
                $startDate   = now()->startOfYear();
                $groupFormat = 'MONTH(created_at)';
                $labels      = collect(range(1, 12));
                $labelFormat = fn($k) => (string)$k;
        }

        $baseQuery = \App\Models\Order::where('user_id', $vendorId)
            ->where('status', 'returned');

        // ── KPIs ─────────────────────────────────────────────────────────────
        $totalReturns      = (clone $baseQuery)->count();
        $totalRefundAmount = (clone $baseQuery)->sum('total_amount');

        $totalOrders = \App\Models\Order::where('user_id', $vendorId)->count();
        $returnRate  = $totalOrders > 0 ? ($totalReturns / $totalOrders) * 100 : 0;

        // ── Chart ─────────────────────────────────────────────────────────────
        $chartRaw = (clone $baseQuery)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("{$groupFormat} as grp, COUNT(*) as count")
            ->groupBy('grp')
            ->pluck('count', 'grp')
            ->toArray();

        $chart = $labels->map(function ($label) use ($chartRaw, $labelFormat) {
            $key = $labelFormat($label);
            return ['label' => $label, 'count' => (int)($chartRaw[$key] ?? 0)];
        })->values()->toArray();

        // ── Payment Method Breakdown (grouped by payment_method since no return_reason column) ──
        $reasonsRaw = (clone $baseQuery)
            ->selectRaw('payment_method, COUNT(*) as cnt')
            ->groupBy('payment_method')
            ->orderByDesc('cnt')
            ->get();

        $reasons = $reasonsRaw->map(fn($r) => [
            'reason' => $r->payment_method ?: 'Not Specified',
            'count'  => (int)$r->cnt,
        ])->values()->toArray();

        // ── Recent Returns ────────────────────────────────────────────────────
        $recent = (clone $baseQuery)
            ->with('customer')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'kpi' => [
                'total_returns'      => $totalReturns,
                'total_refund_amount'=> (float)$totalRefundAmount,
                'return_rate'        => round($returnRate, 2),
            ],
            'chart'          => $chart,
            'reasons'        => $reasons,
            'recent_returns' => $recent,
        ]);
    }

    public function cancels(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $period   = $request->get('period', 'monthly');
        $page     = $request->get('page', 1);
        $perPage  = $request->get('per_page', 10);

        // ── Date range & grouping ─────────────────────────────────────────────
        switch ($period) {
            case 'weekly':
                $startDate   = now()->subDays(6)->startOfDay();
                $groupFormat = 'DATE(created_at)';
                $labels      = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
                $labelFormat = fn($k) => $k;
                break;
            case 'yearly':
                $startDate   = now()->startOfYear()->subYears(4);
                $groupFormat = 'YEAR(created_at)';
                $labels      = collect(range(now()->year - 4, now()->year));
                $labelFormat = fn($k) => (string)$k;
                break;
            default: // monthly
                $startDate   = now()->startOfYear();
                $groupFormat = 'MONTH(created_at)';
                $labels      = collect(range(1, 12));
                $labelFormat = fn($k) => (string)$k;
        }

        $baseQuery = \App\Models\Order::where('user_id', $vendorId)
            ->where('status', 'cancelled');

        // ── KPIs ─────────────────────────────────────────────────────────────
        $totalCancels      = (clone $baseQuery)->count();
        $totalLostRevenue  = (clone $baseQuery)->sum('total_amount');

        $totalOrders = \App\Models\Order::where('user_id', $vendorId)->count();
        $cancelRate  = $totalOrders > 0 ? ($totalCancels / $totalOrders) * 100 : 0;

        // Unpaid cancels (could not complete payment)
        $unpaidCancels = (clone $baseQuery)->where('payment_status', 'unpaid')->count();

        // ── Chart ─────────────────────────────────────────────────────────────
        $chartRaw = (clone $baseQuery)
            ->where('created_at', '>=', $startDate)
            ->selectRaw("{$groupFormat} as grp, COUNT(*) as count")
            ->groupBy('grp')
            ->pluck('count', 'grp')
            ->toArray();

        $chart = $labels->map(function ($label) use ($chartRaw, $labelFormat) {
            $key = $labelFormat($label);
            return ['label' => $label, 'count' => (int)($chartRaw[$key] ?? 0)];
        })->values()->toArray();

        // ── Payment Method Breakdown ──────────────────────────────────────────
        $breakdownRaw = (clone $baseQuery)
            ->selectRaw('payment_method, COUNT(*) as cnt')
            ->groupBy('payment_method')
            ->orderByDesc('cnt')
            ->get();

        $breakdown = $breakdownRaw->map(fn($r) => [
            'label' => $r->payment_method ?: 'Not Specified',
            'count' => (int)$r->cnt,
        ])->values()->toArray();

        // ── Recent Cancellations ──────────────────────────────────────────────
        $recent = (clone $baseQuery)
            ->with('customer')
            ->latest()
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'kpi' => [
                'total_cancels'     => $totalCancels,
                'total_lost_revenue'=> (float)$totalLostRevenue,
                'cancel_rate'       => round($cancelRate, 2),
                'unpaid_cancels'    => $unpaidCancels,
            ],
            'chart'          => $chart,
            'breakdown'      => $breakdown,
            'recent_cancels' => $recent,
        ]);
    }

    public function expenses(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;
        $period   = $request->get('period', 'monthly');
        $page     = $request->get('page', 1);
        $perPage  = $request->get('per_page', 10);

        // ── Date range & grouping ─────────────────────────────────────────────
        switch ($period) {
            case 'weekly':
                $startDate   = now()->subDays(6)->startOfDay();
                $groupFormat = 'DATE(expense_date)';
                $labels      = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
                $labelFormat = fn($k) => $k;
                break;
            case 'yearly':
                $startDate   = now()->startOfYear()->subYears(4);
                $groupFormat = 'YEAR(expense_date)';
                $labels      = collect(range(now()->year - 4, now()->year));
                $labelFormat = fn($k) => (string)$k;
                break;
            default: // monthly
                $startDate   = now()->startOfYear();
                $groupFormat = 'MONTH(expense_date)';
                $labels      = collect(range(1, 12));
                $labelFormat = fn($k) => (string)$k;
        }

        $baseQuery = \App\Models\VendorExpense::where('user_id', $vendorId);

        // ── KPIs ─────────────────────────────────────────────────────────────
        $totalExpenses  = (clone $baseQuery)->sum('amount');
        $totalCount     = (clone $baseQuery)->count();
        $avgExpense     = $totalCount > 0 ? $totalExpenses / $totalCount : 0;

        $periodExpenses = (clone $baseQuery)
            ->where('expense_date', '>=', $startDate)
            ->sum('amount');

        // ── Chart ─────────────────────────────────────────────────────────────
        $chartRaw = (clone $baseQuery)
            ->where('expense_date', '>=', $startDate)
            ->selectRaw("{$groupFormat} as grp, SUM(amount) as total")
            ->groupBy('grp')
            ->pluck('total', 'grp')
            ->toArray();

        $chart = $labels->map(function ($label) use ($chartRaw, $labelFormat) {
            $key = $labelFormat($label);
            return ['label' => $label, 'amount' => (float)($chartRaw[$key] ?? 0)];
        })->values()->toArray();

        // ── Status Breakdown ──────────────────────────────────────────────────
        $breakdownRaw = (clone $baseQuery)
            ->selectRaw('status, SUM(amount) as total, COUNT(*) as cnt')
            ->groupBy('status')
            ->orderByDesc('total')
            ->get();

        $breakdown = $breakdownRaw->map(fn($r) => [
            'label'  => $r->status ?: 'N/A',
            'amount' => (float)$r->total,
            'count'  => (int)$r->cnt,
        ])->values()->toArray();

        // ── Recent Expenses (paginated) ───────────────────────────────────────
        $recent = (clone $baseQuery)
            ->latest('expense_date')
            ->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'kpi' => [
                'total_expenses'  => (float)$totalExpenses,
                'period_expenses' => (float)$periodExpenses,
                'avg_expense'     => (float)$avgExpense,
                'total_count'     => $totalCount,
            ],
            'chart'            => $chart,
            'breakdown'        => $breakdown,
            'recent_expenses'  => $recent,
        ]);
    }

    public function coupons(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;

        // Total discount given via orders
        $couponsImpact = \App\Models\Order::where('user_id', $vendorId)
            ->sum('discount_amount');

        $activeCoupons = \App\Models\Coupon::where('vendor_id', $vendorId)
            ->where('is_active', true)
            ->count();

        $totalCoupons = \App\Models\Coupon::where('vendor_id', $vendorId)->count();

        return response()->json([
            'total_discount_given' => (float) $couponsImpact,
            'active_coupons'       => $activeCoupons,
            'total_coupons'        => $totalCoupons,
        ]);
    }

    public function productPerformance(Request $request)
    {
        $vendorId = $request->user()->vendor_id ?? $request->user()->id;

        $products = \App\Models\Product::where('vendor_id', $vendorId)
            ->leftJoin('order_items', 'products.id', '=', 'order_items.product_id')
            ->leftJoin('orders', function ($join) {
                $join->on('order_items.order_id', '=', 'orders.id')
                     ->whereNotIn('orders.status', ['cancelled', 'returned']);
            })
            ->select([
                'products.id',
                'products.name',
                'products.thumbnail',
                'products.sale_price',
                'products.stock_qty',
                \DB::raw('COALESCE(SUM(order_items.quantity), 0) as total_sold'),
                \DB::raw('COALESCE(SUM(order_items.total_price), 0) as total_revenue'),
            ])
            ->groupBy('products.id', 'products.name', 'products.thumbnail', 'products.sale_price', 'products.stock_qty')
            ->orderByDesc('total_sold')
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
        $totalCustomers = \App\Models\Customer::where('vendor_id', $vendorId)->count();
        
        $topCustomers = \App\Models\Customer::where('vendor_id', $vendorId)
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
