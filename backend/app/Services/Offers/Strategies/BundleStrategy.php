<?php

namespace App\Services\Offers\Strategies;

use App\Models\Promotion;
use App\Services\Offers\OfferStrategyInterface;

class BundleStrategy implements OfferStrategyInterface
{
    /**
     * rules schema example:
     * {
     *   "required_items": [10, 15, 22] // product IDs that must be in cart together
     * }
     */
    public function isEligible(Promotion $offer, array $cartItems): bool
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $requiredItems = $rules['required_items'] ?? [];

        if (empty($requiredItems)) return false;

        $cartProductIds = array_column($cartItems, 'product_id');
        
        // Are all required bundle items in the cart?
        foreach ($requiredItems as $reqId) {
            if (!in_array($reqId, $cartProductIds)) {
                return false;
            }
        }

        return true;
    }

    public function apply(Promotion $offer, array $cartItems): array
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $requiredItems = $rules['required_items'] ?? [];

        if (empty($requiredItems)) {
            return ['items' => $cartItems, 'discount_applied' => 0];
        }

        $discountApplied = 0;
        
        // Figure out max number of bundles we can make
        // Find minimum quantity across all required items in the cart
        $bundleVoltages = [];
        foreach ($requiredItems as $reqId) {
            foreach ($cartItems as $item) {
                if ($item['product_id'] == $reqId) {
                    $bundleVoltages[$reqId] = ($bundleVoltages[$reqId] ?? 0) + $item['quantity'];
                }
            }
        }

        // if we are missing any item theoretically, we can't make a bundle
        if (count($bundleVoltages) < count($requiredItems)) {
             return ['items' => $cartItems, 'discount_applied' => 0];
        }

        $numberOfBundles = min($bundleVoltages);

        if ($numberOfBundles <= 0) {
            return ['items' => $cartItems, 'discount_applied' => 0];
        }

        // Apply discount. A bundle discount applies to the grouped total, or each item.
        // Let's assume the discount is applied proportionally or to the total bundle cost.
        $bundleTotalValue = 0;
        $bundleItemsApplied = [];

        foreach ($cartItems as &$item) {
            if (in_array($item['product_id'], $requiredItems)) {
                // Apply the cost of 1 item per bundle * number of bundles
                $itemTotalForBundle = $item['price'] * $numberOfBundles;
                $bundleTotalValue += $itemTotalForBundle;

                if (!isset($item['applied_offers'])) {
                    $item['applied_offers'] = [];
                }
                $item['applied_offers'][] = $offer->id;
            }
        }

        // Calculate total discount
        if ($offer->discount_unit === 'percentage') {
            $discountApplied = $bundleTotalValue * ($offer->discount_value / 100);
        } elseif ($offer->discount_unit === 'fixed') {
            // A fixed discount off the *entire* bundle price (e.g. $10 off the bundle)
            $discountApplied = $offer->discount_value * $numberOfBundles;
            $discountApplied = min($discountApplied, $bundleTotalValue); // can't discount more than it costs
        }

        return [
            'items' => $cartItems,
            'discount_applied' => $discountApplied
        ];
    }
}
