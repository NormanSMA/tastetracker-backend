<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Reglas dinámicas para imagen
        $imageRules = $this->isMethod('post') ? 'nullable|image|max:2048' : 'nullable|image|max:2048';

        return [
            'category_id' => 'required|exists:categories,id', // Debe existir la categoría
            'name' => 'required|string|max:100|unique:products,name,' . $this->route('product'),
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'image' => $imageRules,
            'is_active' => 'boolean'
        ];
    }
}
