<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UserRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Auth::guest();
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:50'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'accepted_contract' => ['required', 'boolean', 'accepted'],
        ];
    }
}
