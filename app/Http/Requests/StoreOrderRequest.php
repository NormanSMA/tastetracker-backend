<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'area_id' => 'required|exists:areas,id',
            'table_number' => 'nullable|string',
            'order_type' => 'required|in:dine_in,takeaway,delivery',
            'customer_id' => 'nullable|exists:users,id', // Opcional si es cliente registrado
            'notes' => 'nullable|string',
            
            // Validación del Array de Items
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.notes' => 'nullable|string',
        ];
    }
}
