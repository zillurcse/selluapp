<?php

namespace App\Services\Offers;

use App\Models\Promotion;
use Illuminate\Support\Collection;

class OfferCalculationService
{
    /**
     * @param array $cartItems array of items like ['product_id' => 1, 'variant_id' => null, 'price' => 100, 'quantity' => 2]
     * @param int|null $vendorId The vendor ID to fetch offers for
     * @return array [
     *     'original_total' => float, 
     *     'discount_total' => float, 
     *     'final_total' => float, 
     *     'applied_offers' => array,
     *     'items' => array // Items with 'applied_offers' keys attached
     * ]
     */
    public function calculateDiscounts(array $cartItems, ?int $vendorId = null): array
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

        // Ensure we never discount more than the cart value
        $discountTotal = min($discountTotal, $originalTotal);

        return $this->formatResult($cartItems, $originalTotal, $discountTotal, $appliedOffersLog);
    }

    private function formatResult(array $cartItems, float $originalTotal, float $discountTotal, array $appliedOffersLog): array
    {
        return [
            'original_total' => round($originalTotal, 2),
            'discount_total' => round($discountTotal, 2),
            'final_total' => round(max(0, $originalTotal - $discountTotal), 2),
            'applied_offers' => $appliedOffersLog,
            'items' => $cartItems
        ];
    }
}
