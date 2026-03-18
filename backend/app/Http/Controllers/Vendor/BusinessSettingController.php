<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\BusinessSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class BusinessSettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('auth:sanctum'),
        ];
    }

    /**
     * Display a listing of the business settings for a group.
     */
    public function index(Request $request)
    {
        $request->validate([
            'group' => 'required|string',
        ]);

        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;
        $group = $request->input('group');

        $settings = BusinessSetting::where('vendor_id', $vendorId)
            ->where('group', $group)
            ->get()
            ->pluck('value', 'type')
            ->map(function ($val) {
                $decoded = json_decode($val, true);
                return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
            });

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Update or create business settings.
     */
    public function update(Request $request)
    {
        $request->validate([
            'group' => 'required|string',
        ]);

        $user = Auth::user();
        $vendorId = $user->vendor_id ?? $user->id;
        $group = $request->input('group');
        // All other fields in the request are considered settings
        $settings = $request->except(['group']);

        foreach ($settings as $type => $value) {
            $saveValue = is_array($value) ? json_encode($value) : $value;

            BusinessSetting::updateOrCreate(
                [
                    'vendor_id' => $vendorId,
                    'type' => $type
                ],
                [
                    'group' => $group,
                    'value' => $saveValue
                ]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Settings updated successfully'
        ]);
    }
}
