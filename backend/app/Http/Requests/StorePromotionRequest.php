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
            'type' => 'required|in:flash_sale,flat_discount,buy_x_get_y,bundle,category,variant',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'target' => 'required|in:all,selected,categories',
            'target_ids' => [
                'nullable',
                'array',
                \Illuminate\Validation\Rule::requiredIf(fn () => in_array($this->target, ['selected', 'categories']) && !in_array($this->type, ['buy_x_get_y', 'bundle']))
            ],
            'discount_value' => 'required|numeric|min:0',
            'discount_unit' => 'required|in:percentage,fixed',
            'banner' => 'nullable',
            'is_active' => 'boolean',
            'is_stackable' => 'boolean',
            'priority' => 'integer|min:0',
            'rules' => 'nullable|json',
        ];
    }
}
