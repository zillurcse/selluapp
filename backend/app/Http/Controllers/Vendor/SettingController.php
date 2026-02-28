<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view', only: ['index']),
            new Middleware('permission:settings.manage', only: ['update']),
        ];
    }

    /**
     * Display a listing of the settings.
     */
    public function index(Request $request)
    {
        $query = ShopSetting::where('user_id', Auth::id());

        if ($request->has('group')) {
            $query->where('group', $request->input('group'));
        }

        // Return as key => value pairs for easy frontend usage
        $settings = $query->get()->pluck('value', 'key')->map(function ($val, $key) {
            // Attempt to decode JSON if possible
            $decoded = json_decode($val, true);
            $parsedVal = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;

            // If the value is a file path starting with 'shop/', return the full URL
            if (is_string($parsedVal) && str_starts_with($parsedVal, 'shop/')) {
                return Storage::disk('public')->url($parsedVal);
            }

            return $parsedVal;
        });

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Store or update settings in storage.
     */
    public function update(Request $request)
    {
        $request->validate([
            'group' => 'required|string',
            'settings' => 'required', // Can be array or string if FormData is used
        ]);

        $userId = Auth::id();
        $group = $request->input('group');
        $settings = $request->input('settings');
        
        // If settings is sent as a JSON string via FormData, decode it
        if (is_string($settings)) {
            $settings = json_decode($settings, true) ?? [];
        }

        if ($group === 'shop_domain') {
            if (!empty($settings['subDomain'])) {
                $exists = ShopSetting::where('key', 'subDomain')
                    ->where('value', $settings['subDomain'])
                    ->where('user_id', '!=', $userId)
                    ->exists();
                if ($exists) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'subDomain' => ['The subdomain has already been taken.']
                    ]);
                }
            }

            if (!empty($settings['customDomain'])) {
                $exists = ShopSetting::where('key', 'customDomain')
                    ->where('value', $settings['customDomain'])
                    ->where('user_id', '!=', $userId)
                    ->exists();
                if ($exists) {
                    throw \Illuminate\Validation\ValidationException::withMessages([
                        'customDomain' => ['The custom domain has already been taken.']
                    ]);
                }
            }
        }

        foreach ($settings as $key => $value) {
            // Encode arrays to JSON string before saving
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

        // Handle File Uploads specifically
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $url = Storage::disk('public')->putFile('shop/' . $userId . '/settings', $file);

                ShopSetting::updateOrCreate(
                    [
                        'user_id' => $userId,
                        'group' => $group,
                        'key' => $key
                    ],
                    ['value' => $url]
                );
            }
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Settings updated successfully'
        ]);
    }
}
