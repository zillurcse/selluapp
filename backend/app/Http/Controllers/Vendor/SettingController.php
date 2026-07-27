<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use App\Services\ShopDomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class SettingController extends Controller implements HasMiddleware
{
    public function __construct(
        private ShopDomainService $domainService
    ) {}

    public static function middleware(): array
    {
        return [
            new Middleware('permission:settings.view|website.view', only: ['index', 'checkSubdomain']),
            new Middleware('permission:settings.manage|website.manage', only: ['update', 'verifyCustomDomain', 'removeCustomDomain']),
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
                $allSettings[$group] = $group === 'shop_domain'
                    ? $this->domainService->getDomainSettingsPayload($userId)
                    : $this->getSettingsByGroup($userId, $group);
            }

            return response()->json([
                'status' => 'success',
                'data' => $allSettings
            ]);
        }

        // Backward compatibility for single group retrieval
        $group = $request->input('group');

        if ($group === 'shop_domain') {
            return response()->json([
                'status' => 'success',
                'data' => $this->domainService->getDomainSettingsPayload($userId),
            ]);
        }

        $settings = $this->getSettingsByGroup($userId, $group);

        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }

    /**
     * Check if a subdomain is available.
     */
    public function checkSubdomain(Request $request)
    {
        $request->validate([
            'subdomain' => 'required|string|max:63',
        ]);

        $userId = Auth::id();
        $subdomain = $this->domainService->normalizeSubdomain($request->input('subdomain'));

        if (!$subdomain || !$this->domainService->isValidSubdomainFormat($subdomain)) {
            return response()->json([
                'status' => 'error',
                'available' => false,
                'message' => 'Subdomain must be 3–63 characters and contain only letters, numbers, and hyphens.',
            ]);
        }

        if ($this->domainService->isSubdomainReserved($subdomain)) {
            return response()->json([
                'status' => 'error',
                'available' => false,
                'message' => 'This subdomain is reserved and cannot be used.',
            ]);
        }

        $current = $this->domainService->getEffectiveSubdomain($userId);
        $available = $subdomain === $current
            || $this->domainService->isSubdomainAvailable($subdomain, $userId);

        return response()->json([
            'status' => 'success',
            'available' => $available,
            'subdomain' => $subdomain,
            'full_domain' => "{$subdomain}.{$this->domainService->platformDomain()}",
            'message' => $available
                ? 'This subdomain is available.'
                : 'This subdomain is already taken.',
        ]);
    }

    /**
     * Verify custom domain DNS records.
     */
    public function verifyCustomDomain(Request $request)
    {
        $userId = Auth::id();
        $result = $this->domainService->verifyCustomDomainDns($userId);

        Cache::forget('storefront_index_' . $userId . '_essential');
        Cache::forget('storefront_index_' . $userId . '_full');

        return response()->json([
            'status' => $result['verified'] ? 'success' : 'error',
            'verified' => $result['verified'],
            'message' => $result['message'],
            'checks' => $result['checks'] ?? [],
            'data' => $this->domainService->getDomainSettingsPayload($userId),
        ], $result['verified'] ? 200 : 422);
    }

    /**
     * Remove the vendor's custom domain.
     */
    public function removeCustomDomain(Request $request)
    {
        $userId = Auth::id();
        $oldDomain = $this->domainService->getCustomDomain($userId);

        ShopSetting::where('user_id', $userId)
            ->where('group', 'shop_domain')
            ->whereIn('key', ['customDomain', 'customDomainVerified'])
            ->delete();

        $this->domainService->invalidateDomainCaches($oldDomain, null);
        Cache::forget('storefront_index_' . $userId . '_essential');
        Cache::forget('storefront_index_' . $userId . '_full');

        return response()->json([
            'status' => 'success',
            'message' => 'Custom domain removed successfully.',
            'data' => $this->domainService->getDomainSettingsPayload($userId),
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

                $oldDomain = $group === 'shop_domain' ? $this->domainService->getCustomDomain($userId) : null;

                if ($group === 'shop_domain') {
                    $settings = $this->prepareDomainSettingsForSave($userId, $settings);
                    $this->validateDomainSettings($userId, $settings);
                }

                $this->saveSettingsGroup($userId, $group, $settings);

                if ($group === 'shop_domain') {
                    $this->domainService->invalidateDomainCaches($oldDomain, $settings['customDomain'] ?? null);
                }
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

            $oldDomain = null;
            if ($group === 'shop_domain') {
                $oldDomain = $this->domainService->getCustomDomain($userId);
                $settings = $this->prepareDomainSettingsForSave($userId, $settings);
                $this->validateDomainSettings($userId, $settings);
            }

            $this->saveSettingsGroup($userId, $group, $settings);

            if ($group === 'shop_domain') {
                $this->domainService->invalidateDomainCaches($oldDomain, $settings['customDomain'] ?? null);
            }
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
        Cache::forget('storefront_index_' . $userId . '_essential');
        Cache::forget('storefront_index_' . $userId . '_full');

        $responseData = [];
        if (($request->input('group') ?? null) === 'shop_domain') {
            $responseData['data'] = $this->domainService->getDomainSettingsPayload($userId);
        }

        return response()->json(array_merge([
            'status' => 'success',
            'message' => 'Settings updated successfully'
        ], $responseData));
    }

    /**
     * Normalize and apply domain-specific save rules.
     */
    private function prepareDomainSettingsForSave(int $userId, array $settings): array
    {
        if (array_key_exists('subDomain', $settings)) {
            $settings['subDomain'] = $this->domainService->normalizeSubdomain($settings['subDomain']) ?? '';
        }

        if (array_key_exists('customDomain', $settings)) {
            $normalized = $this->domainService->normalizeCustomDomain($settings['customDomain']);
            $settings['customDomain'] = $normalized ?? '';

            $previous = $this->domainService->getCustomDomain($userId);
            if ($normalized !== $previous) {
                $settings['customDomainVerified'] = false;
            }
        }

        $currentSub = $this->domainService->getSavedSubdomain($userId)
            ?? $this->domainService->getDefaultSubdomain($userId);

        if (!empty($settings['subDomain']) && $currentSub && $settings['subDomain'] !== $currentSub) {
            $settings['subDomainLocked'] = true;
        }

        unset(
            $settings['platformDomain'],
            $settings['platformIp'],
            $settings['cnameTarget'],
            $settings['subdomainUrl'],
            $settings['customDomainUrl']
        );

        return $settings;
    }

    /**
     * Save a group of settings
     */
    private function saveSettingsGroup($userId, $group, $settings)
    {
        foreach ($settings as $key => $value) {
            if ($value === null) {
                continue;
            }

            if ($value === '' && in_array($key, ['customDomain'], true)) {
                ShopSetting::where('user_id', $userId)
                    ->where('group', $group)
                    ->where('key', $key)
                    ->delete();
                continue;
            }

            $saveValue = is_bool($value)
                ? ($value ? 'true' : 'false')
                : (is_array($value) ? json_encode($value) : $value);
            
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
            $subdomain = $settings['subDomain'];

            if (!$this->domainService->isValidSubdomainFormat($subdomain)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subDomain' => ['Subdomain must be 3–63 characters and contain only letters, numbers, and hyphens.'],
                ]);
            }

            if ($this->domainService->isSubdomainReserved($subdomain)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subDomain' => ['This subdomain is reserved and cannot be used.'],
                ]);
            }

            $current = $this->domainService->getEffectiveSubdomain($userId);
            if ($this->domainService->isSubdomainLocked($userId) && $subdomain !== $current) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subDomain' => ['You can only change your subdomain once.'],
                ]);
            }

            if ($subdomain !== $current && !$this->domainService->isSubdomainAvailable($subdomain, $userId)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'subDomain' => ['The subdomain has already been taken.'],
                ]);
            }
        }

        if (!empty($settings['customDomain'])) {
            $domain = $settings['customDomain'];

            if (!$this->domainService->isValidCustomDomainFormat($domain)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customDomain' => ['Please enter a valid domain (e.g. myshop.com).'],
                ]);
            }

            if ($domain === $this->domainService->platformDomain()
                || str_ends_with($domain, '.' . $this->domainService->platformDomain())) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customDomain' => ['You cannot use the platform domain as a custom domain.'],
                ]);
            }

            $currentCustom = $this->domainService->getCustomDomain($userId);
            if ($domain !== $currentCustom && !$this->domainService->isCustomDomainAvailable($domain, $userId)) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'customDomain' => ['The custom domain has already been taken.'],
                ]);
            }
        }
    }
}
