<?php

namespace App\Services;

use App\Models\ShopSetting;
use App\Models\User;
use App\Models\VendorProfile;
use Illuminate\Support\Facades\Cache;

class ShopDomainService
{
    public function platformDomain(): string
    {
        return (string) config('shop.platform_domain', 'selluee.test');
    }

    public function platformIp(): ?string
    {
        $ip = config('shop.platform_ip');

        return $ip ? (string) $ip : null;
    }

    public function normalizeCustomDomain(?string $domain): ?string
    {
        if ($domain === null) {
            return null;
        }

        $domain = trim(strtolower($domain));
        $domain = preg_replace('/^https?:\/\//', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        $domain = rtrim($domain, '/');

        return $domain !== '' ? $domain : null;
    }

    public function normalizeSubdomain(?string $subdomain): ?string
    {
        if ($subdomain === null) {
            return null;
        }

        $subdomain = trim(strtolower($subdomain));
        $subdomain = preg_replace('/[^a-z0-9-]/', '', $subdomain);
        $subdomain = trim($subdomain, '-');

        return $subdomain !== '' ? $subdomain : null;
    }

    public function getDefaultSubdomain(int $userId): ?string
    {
        $profile = VendorProfile::where('user_id', $userId)->first();

        if ($profile?->store_slug) {
            return $this->normalizeSubdomain($profile->store_slug);
        }

        $user = User::find($userId);
        if ($user?->name) {
            return $this->normalizeSubdomain(
                strtolower(preg_replace('/[^a-z0-9]/', '', $user->name))
            );
        }

        return null;
    }

    public function getSavedSubdomain(int $userId): ?string
    {
        $value = ShopSetting::where('user_id', $userId)
            ->where('group', 'shop_domain')
            ->where('key', 'subDomain')
            ->value('value');

        return $this->normalizeSubdomain($value);
    }

    public function getEffectiveSubdomain(int $userId): ?string
    {
        return $this->getSavedSubdomain($userId) ?? $this->getDefaultSubdomain($userId);
    }

    public function getCnameTarget(int $userId): ?string
    {
        $sub = $this->getEffectiveSubdomain($userId);

        return $sub ? "{$sub}.{$this->platformDomain()}" : null;
    }

    public function isSubdomainLocked(int $userId): bool
    {
        return ShopSetting::toBool(
            ShopSetting::where('user_id', $userId)
                ->where('group', 'shop_domain')
                ->where('key', 'subDomainLocked')
                ->value('value')
        );
    }

    public function isCustomDomainVerified(int $userId): bool
    {
        return ShopSetting::toBool(
            ShopSetting::where('user_id', $userId)
                ->where('group', 'shop_domain')
                ->where('key', 'customDomainVerified')
                ->value('value')
        );
    }

    public function getCustomDomain(int $userId): ?string
    {
        $value = ShopSetting::where('user_id', $userId)
            ->where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->value('value');

        return $this->normalizeCustomDomain($value);
    }

    public function ensureDefaultSubdomain(int $userId): void
    {
        if ($this->getSavedSubdomain($userId)) {
            return;
        }

        $default = $this->getDefaultSubdomain($userId);
        if (!$default) {
            return;
        }

        ShopSetting::updateOrCreate(
            [
                'user_id' => $userId,
                'group' => 'shop_domain',
                'key' => 'subDomain',
            ],
            ['value' => $default]
        );
    }

    public function isSubdomainReserved(string $subdomain): bool
    {
        return in_array(strtolower($subdomain), config('shop.reserved_subdomains', []), true);
    }

    public function isSubdomainAvailable(string $subdomain, ?int $excludeUserId = null): bool
    {
        $subdomain = $this->normalizeSubdomain($subdomain);
        if (!$subdomain || strlen($subdomain) < 3 || strlen($subdomain) > 63) {
            return false;
        }

        if ($this->isSubdomainReserved($subdomain)) {
            return false;
        }

        $takenInSettings = ShopSetting::where('group', 'shop_domain')
            ->where('key', 'subDomain')
            ->where('value', $subdomain)
            ->when($excludeUserId, fn ($q) => $q->where('user_id', '!=', $excludeUserId))
            ->exists();

        if ($takenInSettings) {
            return false;
        }

        return !VendorProfile::where('store_slug', $subdomain)
            ->when($excludeUserId, fn ($q) => $q->where('user_id', '!=', $excludeUserId))
            ->exists();
    }

    public function isCustomDomainAvailable(string $domain, ?int $excludeUserId = null): bool
    {
        $domain = $this->normalizeCustomDomain($domain);
        if (!$domain || !$this->isValidCustomDomainFormat($domain)) {
            return false;
        }

        return !ShopSetting::where('group', 'shop_domain')
            ->where('key', 'customDomain')
            ->where('value', $domain)
            ->when($excludeUserId, fn ($q) => $q->where('user_id', '!=', $excludeUserId))
            ->exists();
    }

    public function isValidCustomDomainFormat(string $domain): bool
    {
        return (bool) preg_match(
            '/^(?:[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?\.)+[a-z]{2,}$/',
            $domain
        );
    }

    public function isValidSubdomainFormat(string $subdomain): bool
    {
        $subdomain = $this->normalizeSubdomain($subdomain);

        return $subdomain
            && strlen($subdomain) >= 3
            && strlen($subdomain) <= 63
            && (bool) preg_match('/^[a-z0-9](?:[a-z0-9-]{0,61}[a-z0-9])?$/', $subdomain);
    }

    public function resolveUserIdFromDomain(string $domain): ?int
    {
        $domain = $this->normalizeCustomDomain($domain);
        if (!$domain) {
            return null;
        }

        $cacheKey = 'tenant_domain_' . md5($domain);

        return Cache::remember($cacheKey, 600, function () use ($domain) {
            $customSetting = ShopSetting::where('group', 'shop_domain')
                ->where('key', 'customDomain')
                ->where('value', $domain)
                ->first();

            if ($customSetting) {
                return (int) $customSetting->user_id;
            }

            $parts = explode('.', $domain);
            if (count($parts) < 2) {
                return null;
            }

            $subdomain = $parts[0];
            $platformDomain = $this->platformDomain();

            if (count($parts) >= 3 && implode('.', array_slice($parts, 1)) === $platformDomain) {
                $subSetting = ShopSetting::where('group', 'shop_domain')
                    ->where('key', 'subDomain')
                    ->where('value', $subdomain)
                    ->first();

                if ($subSetting) {
                    return (int) $subSetting->user_id;
                }

                $profile = VendorProfile::where('store_slug', $subdomain)->first();
                if ($profile) {
                    return (int) $profile->user_id;
                }
            }

            return null;
        });
    }

    public function verifyCustomDomainDns(int $userId, ?string $customDomain = null): array
    {
        $domain = $this->normalizeCustomDomain($customDomain ?? $this->getCustomDomain($userId));
        if (!$domain) {
            return [
                'verified' => false,
                'message' => 'No custom domain configured.',
            ];
        }

        $expectedTarget = $this->getCnameTarget($userId);
        if (!$expectedTarget) {
            return [
                'verified' => false,
                'message' => 'Unable to determine the platform subdomain target.',
            ];
        }

        $checks = [
            $this->checkDnsTarget("www.{$domain}", $expectedTarget),
            $this->checkDnsTarget($domain, $expectedTarget),
        ];

        $verified = collect($checks)->contains(fn ($check) => $check['matches']);

        if ($verified) {
            ShopSetting::updateOrCreate(
                [
                    'user_id' => $userId,
                    'group' => 'shop_domain',
                    'key' => 'customDomainVerified',
                ],
                ['value' => 'true']
            );

            return [
                'verified' => true,
                'message' => 'Domain connected successfully.',
                'checks' => $checks,
            ];
        }

        ShopSetting::updateOrCreate(
            [
                'user_id' => $userId,
                'group' => 'shop_domain',
                'key' => 'customDomainVerified',
            ],
            ['value' => 'false']
        );

        return [
            'verified' => false,
            'message' => 'DNS records not detected yet. Point www.' . $domain . ' (CNAME) to ' . $expectedTarget . ' and try again in a few minutes.',
            'checks' => $checks,
        ];
    }

    private function checkDnsTarget(string $host, string $expectedTarget): array
    {
        $expectedTarget = rtrim(strtolower($expectedTarget), '.');
        $host = rtrim(strtolower($host), '.');
        $found = [];

        if (function_exists('dns_get_record')) {
            $cnameRecords = @dns_get_record($host, DNS_CNAME) ?: [];
            foreach ($cnameRecords as $record) {
                if (!empty($record['target'])) {
                    $found[] = rtrim(strtolower($record['target']), '.');
                }
            }

            $aRecords = @dns_get_record($host, DNS_A) ?: [];
            foreach ($aRecords as $record) {
                if (!empty($record['ip'])) {
                    $found[] = $record['ip'];
                }
            }
        }

        $platformIp = $this->platformIp();
        $matches = in_array($expectedTarget, $found, true)
            || ($platformIp && in_array($platformIp, $found, true));

        return [
            'host' => $host,
            'expected' => $expectedTarget,
            'found' => $found,
            'matches' => $matches,
        ];
    }

    public function invalidateDomainCaches(?string $oldDomain, ?string $newDomain): void
    {
        foreach (array_filter([$oldDomain, $newDomain]) as $domain) {
            $domain = $this->normalizeCustomDomain($domain);
            if ($domain) {
                Cache::forget('cors_domain_' . md5($domain));
                Cache::forget('tenant_domain_' . md5($domain));
                Cache::forget('tenant_domain_' . md5('www.' . $domain));
            }
        }
    }

    public function getDomainSettingsPayload(int $userId): array
    {
        $this->ensureDefaultSubdomain($userId);

        $settings = ShopSetting::getGroupSettings($userId, 'shop_domain');
        $effectiveSubdomain = $this->getEffectiveSubdomain($userId);
        $cnameTarget = $this->getCnameTarget($userId);

        return array_merge($settings, [
            'subDomain' => $effectiveSubdomain ?? '',
            'customDomain' => $this->getCustomDomain($userId) ?? '',
            'customDomainVerified' => $this->isCustomDomainVerified($userId),
            'subDomainLocked' => $this->isSubdomainLocked($userId),
            'platformDomain' => $this->platformDomain(),
            'platformIp' => $this->platformIp(),
            'cnameTarget' => $cnameTarget,
            'subdomainUrl' => $cnameTarget ? "https://{$cnameTarget}" : null,
            'customDomainUrl' => !empty($settings['customDomain'])
                ? 'https://' . $this->normalizeCustomDomain($settings['customDomain'])
                : null,
        ]);
    }
}
