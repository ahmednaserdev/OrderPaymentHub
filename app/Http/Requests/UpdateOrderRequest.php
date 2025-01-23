<?php

namespace App\Http\Requests;

use App\Enums\OrderStatus;
use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderRequest extends FormRequest
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
            'items' => 'sometimes|array',
            'items.*.product_name' => 'required_with:items|string',
            'items.*.quantity' => 'required_with:items|integer|min:1',
            'items.*.price' => 'required_with:items|numeric|min:0',
            'user_details' => 'sometimes|array',
            'user_details.*.name' => 'required_with:user_details|string',
            'user_details.*.address' => 'required_with:user_details|string',
            'user_details.*.phone' => 'required_with:user_details|string|min:10',
            'status' => 'sometimes|in:' . implode(',', OrderStatus::values()),
        ];
    }
}
