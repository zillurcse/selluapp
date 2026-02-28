<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class DeliveryController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:delivery.view', only: ['index']),
            new Middleware('permission:delivery.manage', only: ['store']),
        ];
    }

    /**
     * Display a listing of the vendor's delivery settings.
     */
    public function index()
    {
        $userId = Auth::id();
        
        // Fetch only the 'delivery' group for the authenticated user
        $settings = ShopSetting::where('user_id', $userId)
            ->where('group', 'delivery')
            ->pluck('value', 'key')
            ->map(function ($val, $key) {
                // Attempt to decode JSON if possible
                $decoded = json_decode($val, true);
                return (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;
            });

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Store or update the vendor's delivery settings.
     */
    public function store(Request $request)
    {
        $request->validate([
            'redx' => 'nullable|array',
            'pathao' => 'nullable|array',
            'steadfast' => 'nullable|array',
            'personal' => 'nullable|array',
        ]);

        $userId = Auth::id();
        $group = 'delivery';
        $settings = $request->except(['_token', '_method']);

        foreach ($settings as $key => $value) {
            $saveValue = is_array($value) ? json_encode($value) : $value;
            
            ShopSetting::updateOrCreate(
                [
                    'user_id' => $userId,
                    'group' => $group,
                    'key' => $key
                ],
                ['value' => $saveValue]
            );
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Delivery settings updated successfully'
        ]);
    }
}
