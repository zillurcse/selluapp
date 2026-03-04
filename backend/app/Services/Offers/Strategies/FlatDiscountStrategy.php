<?php

namespace App\Services\Offers\Strategies;

use App\Models\Promotion;
use App\Services\Offers\OfferStrategyInterface;

class FlatDiscountStrategy implements OfferStrategyInterface
{
    /**
     * Standard flash sale / flat discount on selected products or all products.
     */
    public function isEligible(Promotion $offer, array $cartItems): bool
    {
        if ($offer->target === 'all') {
            return true;
        }

        if ($offer->target === 'selected') {
            $targetIds = is_string($offer->target_ids) ? json_decode($offer->target_ids, true) : $offer->target_ids;
            if (empty($targetIds)) return false;

            $cartProductIds = array_column($cartItems, 'product_id');
            foreach ($targetIds as $tid) {
                if (in_array($tid, $cartProductIds)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function apply(Promotion $offer, array $cartItems): array
    {
        $discountApplied = 0;

        $targetIds = [];
        if ($offer->target === 'selected') {
             $targetIds = is_string($offer->target_ids) ? json_decode($offer->target_ids, true) : $offer->target_ids;
        }

        foreach ($cartItems as &$item) {
            $isEligibleItem = ($offer->target === 'all' || in_array($item['product_id'], $targetIds));

            if ($isEligibleItem) {
                $itemTotal = $item['price'] * $item['quantity'];
                $itemDiscount = 0;

                if ($offer->discount_unit === 'percentage') {
                    $itemDiscount = $itemTotal * ($offer->discount_value / 100);
                } elseif ($offer->discount_unit === 'fixed') {
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
