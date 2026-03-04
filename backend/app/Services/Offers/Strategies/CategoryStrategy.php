<?php

namespace App\Services\Offers\Strategies;

use App\Models\Promotion;
use App\Models\Product; // Need to know product categories
use App\Services\Offers\OfferStrategyInterface;

class CategoryStrategy implements OfferStrategyInterface
{
    /**
     * rules schema example:
     * {
     *   "category_id": 5
     * }
     */
    public function isEligible(Promotion $offer, array $cartItems): bool
    {
        // Target specifies category ids
        $targetIds = is_string($offer->target_ids) ? json_decode($offer->target_ids, true) : $offer->target_ids;
        if (empty($targetIds)) return false;

        // Check if any cart item belongs to this category.
        // Assuming cartItem has 'category_id' or we need to look it up.
        $productIds = array_column($cartItems, 'product_id');
        
        $matchingProductsCount = Product::whereIn('id', $productIds)
            ->whereIn('category_id', $targetIds)
            ->count();

        return $matchingProductsCount > 0;
    }

    public function apply(Promotion $offer, array $cartItems): array
    {
        $targetIds = is_string($offer->target_ids) ? json_decode($offer->target_ids, true) : $offer->target_ids;
        
        if (empty($targetIds)) {
            return ['items' => $cartItems, 'discount_applied' => 0];
        }

        $productIds = array_column($cartItems, 'product_id');
        
        // Find exactly which products in the cart match the category
        $eligibleProductIds = Product::whereIn('id', $productIds)
            ->whereIn('category_id', $targetIds)
            ->pluck('id')
            ->toArray();

        $discountApplied = 0;

        foreach ($cartItems as &$item) {
            if (in_array($item['product_id'], $eligibleProductIds)) {

                // Skip if this item was completely discounted by a non-stackable offer, handled later by pipeline, but we just calculate:
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
