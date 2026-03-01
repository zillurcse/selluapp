<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
        return [
            'category_ids' => 'required|array',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'name' => 'required|string|max:255',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'product_code' => 'nullable|string|max:100|unique:products,product_code',
            'short_description' => 'nullable|string',
            'description' => 'nullable|string',
            'purchase_price' => 'nullable|numeric',
            'sale_price' => 'required|numeric',
            'discount_price' => 'nullable|numeric',
            'stock_qty' => 'nullable|integer',
            'weight' => 'nullable|numeric|min:0',
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
            'variants' => 'nullable|string', // JSON string for variants
            'is_active' => 'boolean',
            'stock_visibility_state' => 'nullable|string|in:quantity,text,hide',
            'low_stock_warning_qty' => 'nullable|integer',
            'image' => 'nullable', // Allow both file and string (path)
            'thumbnail' => 'nullable',
            'video' => 'nullable',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable',
        ];
    }
}
