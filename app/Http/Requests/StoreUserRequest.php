<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $userId = $this->route('user');

        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($userId)
            ],
            'password' => $this->isMethod('post') ? 'required|string|min:8' : 'nullable|string|min:8',
            'role' => 'required|in:admin,waiter,kitchen,customer',
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048',
            'is_active' => 'boolean'
        ];
    }
}
