<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $productId = $this->route('product')->id ?? $this->product;

        return [
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'name' => 'sometimes|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $productId,
            'product_code' => 'nullable|string|max:100|unique:products,product_code,' . $productId,
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',
            'sale_price' => 'sometimes|numeric',
            'discount_price' => 'nullable|numeric',
            'stock_qty' => 'nullable|integer',
            'has_variants' => 'boolean',
            'is_featured' => 'boolean',
            'is_special' => 'boolean',
            'is_trending' => 'boolean',
            'is_buy_one_get_one' => 'boolean',
            'is_preorder' => 'boolean',
            'is_dropshipping' => 'boolean',
            'discount_value' => 'nullable|numeric',
            'discount_type' => 'nullable|string|in:flat,percent',
            'note' => 'nullable|string',
            'video_url' => 'nullable|string|url',
            'status' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'faqs' => 'nullable|string', // JSON string from frontend
            'specifications' => 'nullable|string', // JSON string from frontend
            'is_active' => 'boolean',
            'image' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'thumbnail' => 'nullable|file|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'video' => 'nullable|file|mimes:mp4,mov,ogg,qt|max:20000',
            'gallery' => 'nullable|array',
            'gallery.*' => 'file|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
