<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'type' => 'required|in:flash_sale,flat_discount,buy_x_get_y,bundle',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target' => 'required|in:all,selected,categories',
            'target_ids' => 'nullable|array|required_if:target,selected,categories',
            'discount_value' => 'required|numeric|min:0',
            'discount_unit' => 'required|in:percentage,fixed',
            'banner' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }
}
