<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FinanceController extends Controller
{
    // ─────────────────────────────────────────────────────────────────────
    //  PAYMENTS
    // ─────────────────────────────────────────────────────────────────────

    public function paymentsIndex(Request $request)
    {
        $query = Payment::with(['user', 'subscription.package'])
            ->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('payment_ref', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) => $u->where('name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%'));
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(15));
    }

    public function paymentsStore(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'amount'         => 'required|numeric|min:0',
            'status'         => 'required|in:pending,completed,failed,refunded',
            'payment_method' => 'nullable|string',
            'gateway'        => 'nullable|string',
            'notes'          => 'nullable|string',
            'paid_at'        => 'nullable|date',
            'subscription_id'=> 'nullable|exists:subscriptions,id',
        ]);

        $validated['payment_ref'] = 'PAY-' . strtoupper(Str::random(8));

        $payment = Payment::create($validated);

        // Auto-create transaction for completed payments
        if ($validated['status'] === 'completed') {
            Transaction::create([
                'user_id'     => $validated['user_id'],
                'payment_id'  => $payment->id,
                'type'        => 'credit',
                'amount'      => $validated['amount'],
                'currency'    => 'BDT',
                'reference'   => $payment->payment_ref,
                'description' => 'Payment received',
            ]);
        }

        return response()->json([
            'message' => 'Payment recorded successfully',
            'payment' => $payment->load('user', 'subscription.package')
        ], 201);
    }

    public function paymentsUpdate(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'status'         => 'sometimes|in:pending,completed,failed,refunded',
            'payment_method' => 'nullable|string',
            'notes'          => 'nullable|string',
            'paid_at'        => 'nullable|date',
        ]);

        $payment->update($validated);

        return response()->json([
            'message' => 'Payment updated',
            'payment' => $payment->load('user', 'subscription.package')
        ]);
    }

    public function paymentsDestroy(Payment $payment)
    {
        $payment->delete();
        return response()->json(['message' => 'Payment deleted']);
    }

    public function paymentsStats()
    {
        return response()->json([
            'total'     => Payment::sum('amount'),
            'completed' => Payment::where('status', 'completed')->sum('amount'),
            'pending'   => Payment::where('status', 'pending')->count(),
            'failed'    => Payment::where('status', 'failed')->count(),
            'this_month'=> Payment::where('status', 'completed')
                            ->whereMonth('created_at', now()->month)->sum('amount'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  SUBSCRIPTIONS
    // ─────────────────────────────────────────────────────────────────────

    public function subscriptionsIndex(Request $request)
    {
        $query = Subscription::with(['user', 'package'])->latest();

        if ($request->search) {
            $query->whereHas('user', fn($u) =>
                $u->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
            );
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        return response()->json($query->paginate(15));
    }

    public function subscriptionsStore(Request $request)
    {
        $validated = $request->validate([
            'user_id'        => 'required|exists:users,id',
            'package_id'     => 'nullable|exists:packages,id',
            'status'         => 'required|in:active,expired,cancelled,pending',
            'amount'         => 'required|numeric|min:0',
            'billing_cycle'  => 'required|in:monthly,yearly,lifetime',
            'starts_at'      => 'nullable|date',
            'ends_at'        => 'nullable|date',
            'payment_method' => 'nullable|string',
            'notes'          => 'nullable|string',
        ]);

        $validated['invoice_number'] = 'INV-' . strtoupper(Str::random(8));

        $subscription = Subscription::create($validated);

        return response()->json([
            'message'      => 'Subscription created',
            'subscription' => $subscription->load('user', 'package')
        ], 201);
    }

    public function subscriptionsUpdate(Request $request, Subscription $subscription)
    {
        $validated = $request->validate([
            'status'     => 'sometimes|in:active,expired,cancelled,pending',
            'package_id' => 'nullable|exists:packages,id',
            'ends_at'    => 'nullable|date',
            'notes'      => 'nullable|string',
        ]);

        if (isset($validated['status']) && $validated['status'] === 'cancelled' && !$subscription->cancelled_at) {
            $validated['cancelled_at'] = now();
        }

        // Apply package upgrades when subscription becomes active
        if (isset($validated['status']) && $validated['status'] === 'active' && $subscription->status !== 'active') {
            $profile = \App\Models\VendorProfile::where('user_id', $subscription->user_id)->first();
            
            if ($profile) {
                $profile->update([
                    'package_id' => $validated['package_id'] ?? $subscription->package_id,
                    'package_expiry_date' => $validated['ends_at'] ?? $subscription->ends_at
                ]);
            }

            // Also settle any pending payments attached to this subscription
            $pendingPayments = $subscription->payments()->where('status', 'pending')->get();
            foreach($pendingPayments as $payment) {
                $payment->update([
                    'status' => 'completed',
                    'paid_at' => now(),
                ]);
                
                \App\Models\Transaction::create([
                    'user_id'     => $payment->user_id,
                    'payment_id'  => $payment->id,
                    'type'        => 'credit',
                    'amount'      => $payment->amount,
                    'currency'    => $payment->currency ?? 'BDT',
                    'reference'   => $payment->payment_ref,
                    'description' => 'Subscription payment received',
                ]);
            }
        }

        $subscription->update($validated);

        return response()->json([
            'message'      => 'Subscription updated',
            'subscription' => $subscription->load('user', 'package')
        ]);
    }

    public function subscriptionsDestroy(Subscription $subscription)
    {
        $subscription->delete();
        return response()->json(['message' => 'Subscription deleted']);
    }

    public function subscriptionsStats()
    {
        return response()->json([
            'active'    => Subscription::where('status', 'active')->count(),
            'expired'   => Subscription::where('status', 'expired')->count(),
            'cancelled' => Subscription::where('status', 'cancelled')->count(),
            'mrr'       => Subscription::where('status', 'active')
                            ->where('billing_cycle', 'monthly')->sum('amount'),
            'arr'       => Subscription::where('status', 'active')
                            ->where('billing_cycle', 'yearly')->sum('amount'),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    //  TRANSACTIONS
    // ─────────────────────────────────────────────────────────────────────

    public function transactionsIndex(Request $request)
    {
        $query = Transaction::with(['user', 'payment'])->latest();

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('reference', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhereHas('user', fn($u) =>
                      $u->where('name', 'like', '%' . $request->search . '%')
                  );
            });
        }

        if ($request->type) {
            $query->where('type', $request->type);
        }

        return response()->json($query->paginate(20));
    }

    public function transactionsStats()
    {
        return response()->json([
            'total_credits' => Transaction::where('type', 'credit')->sum('amount'),
            'total_debits'  => Transaction::where('type', 'debit')->sum('amount'),
            'total_refunds' => Transaction::where('type', 'refund')->sum('amount'),
            'count'         => Transaction::count(),
            'this_month'    => Transaction::whereMonth('created_at', now()->month)->sum('amount'),
        ]);
    }
}
