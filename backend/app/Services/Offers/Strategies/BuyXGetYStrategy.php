<?php

namespace App\Services\Offers\Strategies;

use App\Models\Promotion;
use App\Services\Offers\OfferStrategyInterface;

class BuyXGetYStrategy implements OfferStrategyInterface
{
    /**
     * rules schema example:
     * {
     *   "buy_qty": 2,
     *   "buy_product_id": 10,
     *   "get_qty": 1,
     *   "get_product_id": 10
     * }
     */
    public function isEligible(Promotion $offer, array $cartItems): bool
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $buyQtyRequired = $rules['buy_qty'] ?? 0;
        $buyProductId = $rules['buy_product_id'] ?? null;
        
        if (!$buyProductId || $buyQtyRequired <= 0) return false;

        // Check if cart has enough 'buy' items
        $cartQty = 0;
        foreach ($cartItems as $item) {
            if ($item['product_id'] == $buyProductId) {
                // If the item has already been fully discounted by a non-stackable offer, we shouldn't count it, 
                // but we will handle exact un-discounted qty in the `apply` method. 
                // For eligibility, just check total qty.
                $cartQty += $item['quantity'];
            }
        }

        return $cartQty >= $buyQtyRequired;
    }

    public function apply(Promotion $offer, array $cartItems): array
    {
        $rules = json_decode($offer->rules, true) ?? [];
        $buyQty = $rules['buy_qty'] ?? 1;
        $getQty = $rules['get_qty'] ?? 1;
        $buyProductId = $rules['buy_product_id'] ?? null;
        $getProductId = $rules['get_product_id'] ?? null;

        $discountApplied = 0;

        // Count how many 'buy' items are in the cart
        $totalBuyItems = 0;
        foreach ($cartItems as $item) {
            if ($item['product_id'] == $buyProductId) {
                $totalBuyItems += $item['quantity'];
            }
        }

        // How many times can we apply this? (e.g. bought 4, buy_qty=2 -> applies 2 times)
        $timesToApply = floor($totalBuyItems / $buyQty);
        $totalGetQtyAllowed = $timesToApply * $getQty;

        if ($totalGetQtyAllowed <= 0) {
            return ['items' => $cartItems, 'discount_applied' => 0];
        }

        // Now find the 'get' items in the cart to discount
        $qtyDiscounted = 0;
        foreach ($cartItems as &$item) {
            if ($qtyDiscounted >= $totalGetQtyAllowed) break;

            if ($item['product_id'] == $getProductId) {
                // We can only discount what's available
                $qtyAvailableToDiscount = min($item['quantity'], $totalGetQtyAllowed - $qtyDiscounted);
                
                // Calculate discount per item
                $price = $item['price']; // assuming price is the unit price
                $itemDiscount = 0;
                
                if ($offer->discount_unit === 'percentage') {
                    $itemDiscount = $price * ($offer->discount_value / 100);
                } elseif ($offer->discount_unit === 'fixed') {
                    $itemDiscount = min($price, $offer->discount_value);
                }

                // Add to total
                $discountApplied += ($itemDiscount * $qtyAvailableToDiscount);
                
                // Keep track
                $qtyDiscounted += $qtyAvailableToDiscount;
                
                // Tag item to prevent double discounting later
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
