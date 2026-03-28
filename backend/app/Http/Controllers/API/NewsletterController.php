<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscription;
use App\Models\User;
use App\Mail\ShopMail;
use App\Helpers\EmailHelper;
use Illuminate\Support\Facades\Mail;
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
                
                // Send Welcome Email
                $vendor = User::find($vendorId);
                if ($vendor) {
                    EmailHelper::setMailConfig($vendorId);
                    $data = [
                        'shop_name' => $vendor->name,
                        'name' => $email, // Or subscriber name if available
                        'discount_code' => 'WELCOME10',
                        'shop_url' => config('app.front_url') ?? config('app.url')
                    ];
                    Mail::to($email)->send(new ShopMail($vendorId, 'newsletter_welcome', $data));
                }

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
            'source'    => $request->get('source', 'unknown'),
        ]);

        // Send Welcome Email
        $vendor = User::find($vendorId);
        if ($vendor) {
            EmailHelper::setMailConfig($vendorId);
            $data = [
                'shop_name' => $vendor->name,
                'name' => $email,
                'discount_code' => 'WELCOME10',
                'get_discount_url' => config('app.front_url') ?? config('app.url')
            ];
            Mail::to($email)->send(new ShopMail($vendorId, 'newsletter_welcome', $data));
        }

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
