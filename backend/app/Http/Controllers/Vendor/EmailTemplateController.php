<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Mail\ShopMail;
use Illuminate\Support\Facades\Mail;
use App\Helpers\EmailHelper;

class EmailTemplateController extends Controller
{
    // ... index, store, show, destroy methods remain same ...
    public function index()
    {
        $templates = EmailTemplate::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'data' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'subject' => 'required|string',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $template = EmailTemplate::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'type' => $request->type
            ],
            [
                'subject' => $request->subject,
                'content' => $request->content,
                'is_active' => $request->is_active ?? true
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Template saved successfully',
            'data' => $template
        ]);
    }

    public function test(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'type' => 'required|string',
            'subject' => 'nullable|string',
            'content' => 'nullable|string'
        ]);

        $vendorId = Auth::id();
        EmailHelper::setMailConfig($vendorId);

        $mockData = [
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => $request->email,
            'shop_name' => 'Demo Shop',
            'order_id' => '#ORD-99210',
            'amount' => '$250.00',
            'status' => 'Processing',
            'courier' => 'Standard Delivery',
            'tracking_id' => 'TRK12345678',
            'otp' => '429011',
            'reset_link' => 'https://example.com/reset-password?token=test',
            'verification_link' => 'https://example.com/verify-email?token=test',
            'product_name' => 'Premium Wireless Headphones',
            'discount_code' => 'SAVE20',
            'points' => '500',
            'commission' => '$45.00'
        ];

        try {
            Mail::to($request->email)->send(new ShopMail($vendorId, $request->type, $mockData, $request->subject, $request->content));
            
            return response()->json([
                'status' => 'success',
                'message' => 'Test Email sent successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to send test Email: ' . $e->getMessage()
            ], 500);
        }
    }
}
