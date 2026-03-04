<?php

namespace App\Services\Offers;

use App\Models\Promotion;

interface OfferStrategyInterface
{
    /**
     * Check if the cart satisfies the conditions for this offer.
     *
     * @param Promotion $offer
     * @param array $cartItems
     * @return bool
     */
    public function isEligible(Promotion $offer, array $cartItems): bool;

    /**
     * Apply the discount to the eligible items in the cart.
     * Return modified cart items and the total discount amount applied.
     *
     * @param Promotion $offer
     * @param array $cartItems
     * @return array ['items' => array, 'discount_applied' => float]
     */
    public function apply(Promotion $offer, array $cartItems): array;
}
