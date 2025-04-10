<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PasswordResetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = auth()->user();

        return [
            'email' => [Rule::requiredIf(fn() => is_null($user)),'email'],
            'token' => [Rule::requiredIf(fn() => is_null($user)), 'string'],
            'password' => ['required', 'min:6', 'confirmed']
        ];
    }
}
