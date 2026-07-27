<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\NormalizesProductInput;
use App\Http\Requests\Concerns\ValidatesProductBusinessRules;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class UpdateProductRequest extends FormRequest
{
    use NormalizesProductInput;
    use ValidatesProductBusinessRules;
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
        $product = $this->route('product');
        $productId = is_object($product) ? $product->id : $product;

        return [
            'category_ids' => 'sometimes|array',
            'category_ids.*' => 'exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'unit_id' => 'nullable|exists:units,id',
            'name' => 'sometimes|required|string|max:255',
            'slug' => 'sometimes|nullable|string|max:255',
            'sku' => 'sometimes|required|string|max:100|unique:products,sku,' . $productId,
            'product_code' => 'sometimes|required|string|max:100|unique:products,product_code,' . $productId,
            'short_description' => 'nullable|string',
            'description' => 'sometimes|required|string',
            'purchase_price' => 'nullable|numeric',
            'sale_price' => 'sometimes|numeric',
            'discount_price' => 'nullable|numeric',
            'stock_qty' => 'nullable|integer',
            'weight' => 'sometimes|numeric|min:0',
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
            'status' => 'nullable|string|in:published,draft,pending',
            'dropshipping_url' => 'nullable|url|required_if:is_dropshipping,1,true',
            'dropshipping_sku' => 'nullable|string|max:100',
            'gallery_items' => 'nullable|string',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string',
            'seo_keywords' => 'nullable|string',
            'faqs' => 'nullable|string', // JSON string from frontend
            'specifications' => 'nullable|string', // JSON string from frontend
            'variants' => 'nullable|string', // JSON string for variants
            'is_active' => 'boolean',
            'stock_visibility_state' => 'sometimes|string|in:quantity,text,hide',
            'low_stock_warning_qty' => 'sometimes|integer',
            'image' => 'nullable',
            'thumbnail' => 'nullable',
            'video' => 'nullable',
            'gallery' => 'nullable|array',
            'gallery.*' => 'nullable',
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator) {
            $this->validateProductBusinessRules($validator);
        });
    }
}
