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
        $userId = Auth::id();

        // Support for retrieving multiple groups at once
        if ($request->has('groups') && is_array($request->input('groups'))) {
            $groups = $request->input('groups');
            $allSettings = [];

            foreach ($groups as $group) {
                $allSettings[$group] = $this->getSettingsByGroup($userId, $group);
            }

            return response()->json([
                'status' => 'success',
                'data' => $allSettings
            ]);
        }

        // Backward compatibility for single group retrieval
        $group = $request->input('group');
        $settings = $this->getSettingsByGroup($userId, $group);

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Get settings for a specific group, parsed and formatted
     */
    private function getSettingsByGroup($userId, $group)
    {
        $query = ShopSetting::where('user_id', $userId);
        
        if ($group) {
            $query->where('group', $group);
        }

        return $query->get()->pluck('value', 'key')->map(function ($val, $key) {
            // Attempt to decode JSON if possible
            $decoded = json_decode($val, true);
            $parsedVal = (json_last_error() === JSON_ERROR_NONE) ? $decoded : $val;

            // If the value is a file path starting with 'shop/', return the full URL
            if (is_string($parsedVal) && str_starts_with($parsedVal, 'shop/')) {
                return Storage::disk('public')->url($parsedVal);
            }

            return $parsedVal;
        });
    }

    /**
     * Store or update settings in storage.
     */
    public function update(Request $request)
    {
        $userId = Auth::id();

        // Support for batch updates (multiple groups)
        if ($request->has('groups') && is_array($request->input('groups'))) {
            foreach ($request->input('groups') as $groupData) {
                $group = $groupData['group'] ?? null;
                $settings = $groupData['settings'] ?? [];

                if (!$group || empty($settings)) continue;

                $this->saveSettingsGroup($userId, $group, $settings);
            }
        } else {
            // Backward compatibility for single group update
            $request->validate([
                'group' => 'required|string',
                'settings' => 'required',
            ]);

            $group = $request->input('group');
            $settings = $request->input('settings');
            
            if (is_string($settings)) {
                $settings = json_decode($settings, true) ?? [];
            }

            // Special validation for shop domain
            if ($group === 'shop_domain') {
                 $this->validateDomainSettings($userId, $settings);
            }

            $this->saveSettingsGroup($userId, $group, $settings);
        }

        // Handle File Uploads (per group if specified, or generic)
        if ($request->hasFile('files')) {
            $group = $request->input('group', 'general');
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

        // Invalidate storefront cache for this vendor (both essential and full)
        \Illuminate\Support\Facades\Cache::forget('storefront_index_' . $userId . '_essential');
        \Illuminate\Support\Facades\Cache::forget('storefront_index_' . $userId . '_full');

        return response()->json([
            'status' => 'success',
            'message' => 'Settings updated successfully'
        ]);
    }

    /**
     * Save a group of settings
     */
    private function saveSettingsGroup($userId, $group, $settings)
    {
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
    }

    /**
     * Validate domain settings
     */
    private function validateDomainSettings($userId, $settings)
    {
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
}
