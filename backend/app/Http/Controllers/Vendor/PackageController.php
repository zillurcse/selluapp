<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index()
    {
        $packages = Package::where('is_active', true)->orderBy('price')->get();
        return response()->json($packages);
    }

    public function purchase(Request $request, Package $package)
    {
        $user = auth()->user();
        $profile = $user->vendorProfile;
        
        if (!$profile) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        // Check if there is already a pending subscripton
        $existing = \App\Models\Subscription::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();
            
        if ($existing) {
            return response()->json(['message' => 'You already have a pending upgrade request. Please wait for admin approval.'], 422);
        }

        $billingCycle = $request->input('billing_cycle', 'monthly');
        
        $price = $package->price;
        if ($billingCycle === 'yearly') {
            $price = $price * 12 * 0.8;
        }

        $endsAt = $billingCycle === 'yearly' ? now()->addYear() : now()->addMonth();

        $subscription = \App\Models\Subscription::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'status' => 'pending',
            'amount' => $price,
            'billing_cycle' => $billingCycle,
            'starts_at' => now(),
            'ends_at' => $endsAt,
            'payment_method' => 'system',
        ]);

        if ($price > 0) {
            \App\Models\Payment::create([
                'user_id' => $user->id,
                'subscription_id' => $subscription->id,
                'amount' => $price,
                'currency' => 'USD',
                'status' => 'pending',
                'payment_method' => 'system',
                'payment_ref' => 'REF-' . strtoupper(uniqid()),
                'paid_at' => null,
            ]);
        }

        return response()->json([
            'message' => "Upgrade request for {$package->name} plan submitted. It is pending admin approval.",
            'user' => $user->fresh(['vendorProfile.package', 'roles.permissions'])
        ]);
    }
}
