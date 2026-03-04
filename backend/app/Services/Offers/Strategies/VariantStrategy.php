<?php

namespace App\Services\Offers\Strategies;

use App\Models\Promotion;
use App\Models\ProductVariant;
use App\Services\Offers\OfferStrategyInterface;

class VariantStrategy implements OfferStrategyInterface
{
    /**
     * rules schema example:
     * {
     *   "target_variants": [100, 105, 120]
     * }
     */
    public function isEligible(Promotion $offer, array $cartItems): bool
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $targetVariants = $rules['target_variants'] ?? [];

        if (empty($targetVariants)) return false;

        $hasEligibleVariant = false;

        foreach ($cartItems as $item) {
            // Check if cart item has a variant ID that's in the target list
            if (!empty($item['variant_id']) && in_array($item['variant_id'], $targetVariants)) {
                $hasEligibleVariant = true;
                break;
            }
        }

        return $hasEligibleVariant;
    }

    public function apply(Promotion $offer, array $cartItems): array
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $targetVariants = $rules['target_variants'] ?? [];

        if (empty($targetVariants)) {
            return ['items' => $cartItems, 'discount_applied' => 0];
        }

        $discountApplied = 0;

        foreach ($cartItems as &$item) {
            // Apply discount if this variant is targeted
            if (!empty($item['variant_id']) && in_array($item['variant_id'], $targetVariants)) {

                $itemTotal = $item['price'] * $item['quantity'];
                $itemDiscount = 0;

                if ($offer->discount_unit === 'percentage') {
                    $itemDiscount = $itemTotal * ($offer->discount_value / 100);
                } elseif ($offer->discount_unit === 'fixed') {
                    // Fixed amount per item
                    $itemDiscount = min($item['price'], $offer->discount_value) * $item['quantity'];
                }

                $discountApplied += $itemDiscount;

                if (!isset($item['applied_offers'])) {
                    $item['applied_offers'] = [];
                }
                $item['applied_offers'][] = $offer->id;
            }
        }

        return [
            'items' => $cartItems,
            'discount_applied' => $discountApplied
        ];
    }
}
