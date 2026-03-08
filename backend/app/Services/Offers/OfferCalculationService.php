<?php

namespace App\Services\Offers;

use App\Models\Promotion;
use Illuminate\Support\Collection;

class OfferCalculationService
{
    /**
     * @param array $cartItems array of items like ['product_id' => 1, 'variant_id' => null, 'price' => 100, 'quantity' => 2]
     * @param int|null $vendorId The vendor ID to fetch offers for
     * @param string|null $couponCode The coupon code provided by the user
     * @return array [
     *     'original_total' => float, 
     *     'discount_total' => float, 
     *     'final_total' => float, 
     *     'applied_offers' => array,
     *     'items' => array // Items with 'applied_offers' keys attached
     * ]
     */
    public function calculateDiscounts(array $cartItems, ?int $vendorId = null, ?string $couponCode = null): array
    {
        // Calculate original total before any discounts
        $originalTotal = 0;
        foreach ($cartItems as $item) {
            $originalTotal += ($item['price'] * $item['quantity']);
        }

        if (empty($cartItems) || $originalTotal <= 0) {
            return $this->formatResult($cartItems, $originalTotal, 0, []);
        }

        // Fetch valid, active offers ordered by priority (highest first)
        $query = Promotion::where('is_active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now())
            ->orderByDesc('priority');

        if ($vendorId) {
            $query->where('vendor_id', $vendorId);
        }

        $offers = $query->get();

        $discountTotal = 0;
        $appliedOffersLog = [];
        $hasNonStackableApplied = false;

        foreach ($offers as $offer) {
            // Conflict Resolution: If a non-stackable offer was already applied, stop processing completely
            // Alternatively, we could only skip items, but standard practice for non-stackable is it overrides everything.
            if ($hasNonStackableApplied) {
                break;
            }

            try {
                $strategy = OfferStrategyFactory::make($offer);

                if ($strategy->isEligible($offer, $cartItems)) {
                    // Strip out items from the evaluation pool if we need item-level conflict resolution.
                    // For now, our math allows multiple passes but we let strategies append to 'applied_offers' array per item.
                    
                    $result = $strategy->apply($offer, $cartItems);
                    $appliedDiscount = $result['discount_applied'];

                    if ($appliedDiscount > 0) {
                        $cartItems = $result['items']; // Update cart with tagging for next iterations
                        $discountTotal += $appliedDiscount;
                        
                        $appliedOffersLog[] = [
                            'offer_id' => $offer->id,
                            'offer_title' => $offer->title,
                            'discount_applied' => $appliedDiscount
                        ];

                        if (!$offer->is_stackable) {
                            $hasNonStackableApplied = true;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Log exception if strategy fails but continue calculation
                \Log::error("Offer Evaluation Failed for Offer ID {$offer->id}: " . $e->getMessage());
            }
        }

        // Apply Manual Coupon Code
        $couponError = null;
        if ($couponCode) {
            $coupon = \App\Models\Coupon::where('code', $couponCode)->first();

            if (!$coupon) {
                $couponError = "Invalid coupon code.";
            } elseif (!$coupon->is_active) {
                $couponError = "This coupon is no longer active.";
            } elseif ($coupon->start_date > now()) {
                $couponError = "This coupon is not yet valid.";
            } elseif ($coupon->end_date < now()) {
                $couponError = "This coupon has expired.";
            } elseif ($originalTotal < $coupon->min_purchase) {
                $couponError = "Minimum purchase of ৳{$coupon->min_purchase} required for this coupon.";
            } else {
                $couponDiscount = 0;
                if ($coupon->discount_type === 'percentage') {
                    $couponDiscount = ($originalTotal * ($coupon->discount_amount / 100));
                    if ($coupon->max_discount && $couponDiscount > $coupon->max_discount) {
                        $couponDiscount = $coupon->max_discount;
                    }
                } else {
                    $couponDiscount = $coupon->discount_amount;
                }

                if ($couponDiscount > 0) {
                    $discountTotal += $couponDiscount;
                    $appliedOffersLog[] = [
                        'offer_id' => $coupon->id,
                        'offer_title' => "Coupon: {$coupon->code}",
                        'discount_applied' => round($couponDiscount, 2),
                        'is_coupon' => true
                    ];
                }
            }
        }

        // Ensure we never discount more than the cart value
        $discountTotal = min($discountTotal, $originalTotal);

        return $this->formatResult($cartItems, $originalTotal, $discountTotal, $appliedOffersLog, $couponError);
    }

    private function formatResult(array $cartItems, float $originalTotal, float $discountTotal, array $appliedOffersLog, ?string $couponError = null): array
    {
        return [
            'original_total' => round($originalTotal, 2),
            'discount_total' => round($discountTotal, 2),
            'final_total' => round(max(0, $originalTotal - $discountTotal), 2),
            'applied_offers' => $appliedOffersLog,
            'items' => $cartItems,
            'coupon_error' => $couponError
        ];
    }
}
