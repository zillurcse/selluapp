<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $vendorId = $request->header('X-Vendor-Id');
        $email    = $request->email;

        // Check if already subscribed for this vendor
        $existing = NewsletterSubscription::where('vendor_id', $vendorId)
            ->where('email', $email)
            ->first();

        if ($existing) {
            if ($existing->is_active) {
                return response()->json([
                    'success' => true,
                    'message' => 'You are already subscribed to our newsletter.',
                ]);
            } else {
                $existing->update(['is_active' => true]);
                return response()->json([
                    'success' => true,
                    'message' => 'Your subscription has been reactivated. Welcome back!',
                ]);
            }
        }

        NewsletterSubscription::create([
            'vendor_id' => $vendorId,
            'email'     => $email,
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Thank you for subscribing to our newsletter!',
        ]);
    }

    public function index(Request $request)
    {
        $vendorId = auth()->id();
        $newsletters = NewsletterSubscription::where('vendor_id', $vendorId)
            ->latest()
            ->paginate($request->get('limit', 15));

        return response()->json($newsletters);
    }

    public function destroy($id)
    {
        $vendorId = auth()->id();
        $newsletter = NewsletterSubscription::where('vendor_id', $vendorId)
            ->where('id', $id)
            ->firstOrFail();

        $newsletter->delete();

        return response()->json([
            'success' => true,
            'message' => 'Subscriber has been removed successfully.',
        ]);
    }
}
