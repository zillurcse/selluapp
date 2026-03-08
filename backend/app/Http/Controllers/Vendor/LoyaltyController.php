<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\LoyaltyPointLog;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class LoyaltyController extends Controller
{
    public function getSettings(Request $request)
    {
        $vendorId = Auth::id();
        $settings = ShopSetting::where('user_id', $vendorId)
            ->where('group', 'loyalty_program')
            ->get()
            ->pluck('value', 'key');

        // Dynamic Stats
        $stats = [
            'total_points_circulation' => (int)LoyaltyPointLog::where('vendor_id', $vendorId)->sum('points'),
            'avg_points_per_order' => (float)(LoyaltyPointLog::where('vendor_id', $vendorId)->where('points', '>', 0)->avg('points') ?: 0),
            'active_customers_count' => (int)LoyaltyPointLog::where('vendor_id', $vendorId)->distinct('customer_id')->count('customer_id'),
            'recent_logs' => LoyaltyPointLog::with('customer:id,first_name,last_name')->where('vendor_id', $vendorId)->latest()->limit(5)->get()
        ];

        return response()->json([
            'settings' => [
                'is_enabled' => isset($settings['is_enabled']) ? (bool)$settings['is_enabled'] : false,
                'point_earning_rate' => isset($settings['point_earning_rate']) ? (float)$settings['point_earning_rate'] : 1.0,
                'min_order_total' => isset($settings['min_order_total']) ? (float)$settings['min_order_total'] : 0.0,
                'point_value' => isset($settings['point_value']) ? (float)$settings['point_value'] : 0.1,
            ],
            'stats' => $stats
        ]);
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'is_enabled' => 'required|boolean',
            'point_earning_rate' => 'required|numeric|min:0',
            'min_order_total' => 'required|numeric|min:0',
            'point_value' => 'required|numeric|min:0',
        ]);

        $vendorId = Auth::id();

        foreach ($validated as $key => $value) {
            ShopSetting::updateOrCreate(
                [
                    'user_id' => $vendorId,
                    'group' => 'loyalty_program',
                    'key' => $key
                ],
                ['value' => $value]
            );
        }

        // Clear storefront cache
        Cache::forget('storefront_index_' . $vendorId);
        Cache::forget('storefront_index_global');

        return response()->json([
            'message' => 'Loyalty settings updated successfully',
        ]);
    }

    public function getLogs(Request $request)
    {
        $vendorId = Auth::id();
        $query = LoyaltyPointLog::with('customer:id,first_name,last_name,email')
            ->where('vendor_id', $vendorId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('customer', function($sub) use ($search) {
                    $sub->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('email', 'like', "%$search%");
                })->orWhere('description', 'like', "%$search%");
            });
        }

        $logs = $query->latest()->paginate($request->get('limit', 15));

        // Get summary stats for the vendor
        $stats = [
            'total_awarded' => (int)LoyaltyPointLog::where('vendor_id', $vendorId)->where('points', '>', 0)->sum('points'),
            'total_redeemed' => (int)abs(LoyaltyPointLog::where('vendor_id', $vendorId)->where('points', '<', 0)->sum('points')),
            'active_customers' => (int)LoyaltyPointLog::where('vendor_id', $vendorId)->distinct('customer_id')->count('customer_id'),
        ];

        return response()->json([
            'logs' => $logs,
            'stats' => $stats
        ]);
    }
}
