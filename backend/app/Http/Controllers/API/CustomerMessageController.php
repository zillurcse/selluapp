<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CustomerMessage;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerMessageController extends Controller
{
    private function resolveTenantId(Request $request)
    {
        $domain = $request->header('X-Tenant-Domain') ?? $request->query('domain');

        if (!$domain) {
            return null;
        }

        $domain = preg_replace('/^www\./', '', $domain);

        $customSetting = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->first();

        if ($customSetting) {
            return $customSetting->user_id;
        }

        $parts = explode('.', $domain);
        if (count($parts) >= 2) {
            $subdomain = $parts[0];
            $subSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'subDomain')
                ->where('value', $subdomain)
                ->first();

            if ($subSetting) {
                return $subSetting->user_id;
            }
        }

        return null;
    }

    public function store(Request $request)
    {
        $tenantId = $this->resolveTenantId($request);

        if (!$tenantId) {
            // Fallback to a default or error out
            $tenantId = 5; // Fallback vendor id
        }

        $validator = Validator::make($request->all(), [
            'sender_name'  => 'required|string|max:255',
            'sender_email' => 'required|email|max:255',
            'subject'      => 'nullable|string|max:255',
            'message'      => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $message = CustomerMessage::create([
            'user_id'      => $tenantId,
            'sender_name'  => $request->sender_name,
            'sender_email' => $request->sender_email,
            'subject'      => $request->subject,
            'message'      => $request->message,
            'customer_id'  => auth('sanctum')->check() ? auth('sanctum')->id() : null,
        ]);

        return response()->json([
            'message' => 'Your message has been sent successfully.',
            'data'    => $message
        ], 201);
    }
}
