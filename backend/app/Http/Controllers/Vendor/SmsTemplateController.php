<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\SmsTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\SmsService;

class SmsTemplateController extends Controller
{
    public function index()
    {
        $templates = SmsTemplate::where('user_id', Auth::id())->get();
        return response()->json([
            'status' => 'success',
            'data' => $templates
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|string',
            'content' => 'required|string',
            'is_active' => 'boolean'
        ]);

        $template = SmsTemplate::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'type' => $request->type
            ],
            [
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

    public function show($id)
    {
        $template = SmsTemplate::where('user_id', Auth::id())->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data' => $template
        ]);
    }

    public function destroy($id)
    {
        $template = SmsTemplate::where('user_id', Auth::id())->findOrFail($id);
        $template->delete();
        return response()->json([
            'status' => 'success',
            'message' => 'Template deleted successfully'
        ]);
    }

    public function test(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'type' => 'required|string',
            'content' => 'nullable|string'
        ]);

        $smsService = new SmsService(Auth::id());
        
        $mockData = [
            'otp' => '123456',
            'reset_code' => 'TEST-01',
            'order_id' => '#TEST123',
            'amount' => '৳500',
            'shop_name' => 'Demo Shop',
            'courier' => 'TestCourier',
            'tracking_id' => 'TRK000000',
            'status' => 'Testing',
            'email' => 'staff@example.com',
            'password' => 'secret123'
        ];

        $directMessage = null;
        if ($request->content) {
            // Helper logic to parse temp content since SmsService usually expects directMessage to be final
            $directMessage = $request->content;
            foreach ($mockData as $key => $value) {
                $directMessage = str_replace("{{ $key }}", $value, $directMessage);
            }
        }

        $success = $smsService->send($request->phone, $request->type, $mockData, $directMessage);

        if ($success) {
            return response()->json([
                'status' => 'success',
                'message' => 'Test SMS sent successfully'
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Failed to send test SMS. Check your configuration.'
        ], 500);
    }
}
