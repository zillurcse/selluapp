<?php

namespace App\Http\Requests\Concerns;

use Illuminate\Validation\Validator;

trait ValidatesProductBusinessRules
{
    protected function validateProductBusinessRules(Validator $validator): void
    {
        $this->validateProductVariants($validator);
        $this->validateProductDiscountRules($validator);
        $this->validateSimpleProductStock($validator);
    }

    protected function validateProductVariants(Validator $validator): void
    {
        if (! $this->boolean('has_variants')) {
            return;
        }

        $variants = json_decode($this->input('variants', '[]'), true);

        if (! is_array($variants) || count($variants) === 0) {
            $validator->errors()->add('variants', 'Add at least one variant when product variants are enabled.');

            return;
        }

        $seenSkus = [];

        foreach ($variants as $index => $variant) {
            if (! is_array($variant)) {
                continue;
            }

            $label = 'Variant '.($index + 1);
            $isActive = array_key_exists('is_active', $variant) ? (bool) $variant['is_active'] : true;

            if ($isActive) {
                if (! isset($variant['price']) || ! is_numeric($variant['price']) || (float) $variant['price'] < 0) {
                    $validator->errors()->add("variants.$index.price", "$label price is required and must be zero or greater.");
                }

                if (! isset($variant['stock_qty']) || ! is_numeric($variant['stock_qty']) || (int) $variant['stock_qty'] < 0) {
                    $validator->errors()->add("variants.$index.stock_qty", "$label stock quantity is required and must be zero or greater.");
                }
            }

            if (! empty($variant['sku'])) {
                $sku = strtoupper(trim((string) $variant['sku']));

                if (in_array($sku, $seenSkus, true)) {
                    $validator->errors()->add("variants.$index.sku", "$label SKU is duplicated within this product.");
                } else {
                    $seenSkus[] = $sku;
                }
            }

            if (empty($variant['attributes']) || ! is_array($variant['attributes']) || count($variant['attributes']) === 0) {
                $validator->errors()->add("variants.$index.attributes", "$label must include at least one attribute.");
            }
        }
    }

    protected function validateProductDiscountRules(Validator $validator): void
    {
        if ($this->filled('discount_value') && ! $this->filled('discount_type')) {
            $validator->errors()->add('discount_type', 'Select a discount type when discount value is provided.');
        }

        if ($this->filled('discount_type') && ! $this->filled('discount_value')) {
            $validator->errors()->add('discount_value', 'Enter a discount value when discount type is selected.');
        }

        $salePrice = $this->input('sale_price');
        $discountPrice = $this->input('discount_price');

        if (
            $salePrice !== null
            && $discountPrice !== null
            && is_numeric($salePrice)
            && is_numeric($discountPrice)
            && (float) $discountPrice >= (float) $salePrice
        ) {
            $validator->errors()->add('discount_price', 'Discount price must be less than the sale price.');
        }
    }

    protected function validateSimpleProductStock(Validator $validator): void
    {
        if ($this->boolean('has_variants')) {
            return;
        }

        if ($this->input('stock_qty') === null || $this->input('stock_qty') === '') {
            $validator->errors()->add('stock_qty', 'Stock quantity is required for products without variants.');
        }
    }
}
