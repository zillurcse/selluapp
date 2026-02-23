<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\FraudCheck;
use App\Models\Order;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class FraudCheckController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('package.feature:fraud check'),
            new Middleware('permission:fraud_check.view', only: ['index', 'stats']),
            new Middleware('permission:fraud_check.manage', only: ['updateSetting', 'action']),
        ];
    }

    public function index(Request $request)
    {
        $userId = Auth::id();
        $query = FraudCheck::with('order.customer')
            ->where('user_id', $userId)
            ->where('status', 'pending');

        if ($request->search) {
            $query->whereHas('order', function($q) use ($request) {
                $q->where('invoice_number', 'like', '%' . $request->search . '%');
            });
        }

        $fraudChecks = $query->latest()->paginate($request->per_page ?? 10);

        return response()->json($fraudChecks);
    }

    public function stats()
    {
        $userId = Auth::id();
        
        // Mocking some stats logic based on orders/fraud checks
        $totalScanned = Order::where('user_id', $userId)->count();
        $highRiskBlocked = FraudCheck::where('user_id', $userId)->where('status', 'rejected')->count();
        $suspicious = FraudCheck::where('user_id', $userId)->where('status', 'pending')->count();
        $safeOrders = Order::where('user_id', $userId)->where('requires_manual_review', false)->count();

        $protectionLevel = ShopSetting::where('user_id', $userId)
            ->where('group', 'fraud_protection')
            ->where('key', 'ai_protection_level')
            ->first();

        return response()->json([
            'total_scanned' => $totalScanned,
            'high_risk_blocked' => $highRiskBlocked,
            'suspicious' => $suspicious,
            'safe_orders' => $safeOrders,
            'ai_protection_level' => $protectionLevel ? $protectionLevel->value : 'optimal'
        ]);
    }

    public function updateSetting(Request $request)
    {
        $request->validate([
            'level' => 'required|in:passive,optimal,aggressive'
        ]);

        $userId = Auth::id();
        
        ShopSetting::updateOrCreate(
            ['user_id' => $userId, 'group' => 'fraud_protection', 'key' => 'ai_protection_level'],
            ['value' => $request->level]
        );

        return response()->json(['message' => 'Protection level updated successfully']);
    }

    public function action(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'notes' => 'nullable|string'
        ]);

        $fraudCheck = FraudCheck::where('user_id', Auth::id())->findOrFail($id);
        
        $fraudCheck->update([
            'status' => $request->status,
            'reviewer_notes' => $request->notes,
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now()
        ]);

        // Update the actual order status/risk flag
        $order = $fraudCheck->order;
        if ($request->status === 'approved') {
            $order->update(['requires_manual_review' => false]);
            // You might want to update order status to processing or something here
        } else {
            $order->update(['status' => 'cancelled']);
        }

        return response()->json(['message' => 'Action recorded successfully']);
    }
}
