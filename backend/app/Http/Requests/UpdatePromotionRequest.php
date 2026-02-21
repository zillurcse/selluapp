<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
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
            'title' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|in:flash_sale,flat_discount,buy_x_get_y,bundle',
            'start_date' => 'sometimes|required|date',
            'end_date' => 'sometimes|required|date|after_or_equal:start_date',
            'target' => 'sometimes|required|in:all,selected,categories',
            'target_ids' => 'nullable|array|required_if:target,selected,categories',
            'discount_value' => 'sometimes|required|numeric|min:0',
            'discount_unit' => 'sometimes|required|in:percentage,fixed',
            'banner' => 'nullable|image|max:2048',
            'is_active' => 'boolean',
        ];
    }
}
