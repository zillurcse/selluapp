<?php

namespace App\Services\Offers;

use App\Models\Promotion;
use Exception;

class OfferStrategyFactory
{
    public static function make(Promotion $offer): OfferStrategyInterface
    {
        switch ($offer->type) {
            case 'buy_x_get_y':
                return new Strategies\BuyXGetYStrategy();
            case 'bundle':
                return new Strategies\BundleStrategy();
            case 'category':
                return new Strategies\CategoryStrategy();
            case 'variant':
                return new Strategies\VariantStrategy();
            case 'flash_sale':
            case 'flat_discount':
                return new Strategies\FlatDiscountStrategy();
            default:
                throw new Exception("Unknown offer strategy type: {$offer->type}");
        }
    }
}
