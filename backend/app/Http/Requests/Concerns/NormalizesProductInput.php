<?php

namespace App\Http\Requests\Concerns;

trait NormalizesProductInput
{
    protected function productNullableFields(): array
    {
        return [
            'brand_id',
            'unit_id',
            'slug',
            'sku',
            'product_code',
            'video_url',
            'discount_value',
            'discount_type',
            'discount_price',
            'purchase_price',
            'note',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'dropshipping_url',
            'dropshipping_sku',
        ];
    }

    protected function prepareForValidation(): void
    {
        $normalized = [];

        foreach ($this->productNullableFields() as $field) {
            if ($this->has($field) && $this->input($field) === '') {
                $normalized[$field] = null;
            }
        }

        if ($normalized !== []) {
            $this->merge($normalized);
        }
    }
}
