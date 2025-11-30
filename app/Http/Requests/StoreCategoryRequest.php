<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Permitir a usuarios autenticados
    }

    public function rules(): array
    {
        // Reglas dinámicas: si es POST (crear) la imagen es required, si es PUT (editar) es nullable.
        $imageRules = $this->isMethod('post') ? 'nullable|image|max:2048' : 'nullable|image|max:2048';

        return [
            'name' => 'required|string|max:50|unique:categories,name,' . $this->route('category'),
            'image' => $imageRules,
            'is_active' => 'boolean'
        ];
    }
}
