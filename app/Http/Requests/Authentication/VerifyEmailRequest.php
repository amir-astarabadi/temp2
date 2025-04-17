<?php

namespace App\Http\Requests\Authentication;

use Illuminate\Foundation\Http\FormRequest;

class VerifyEmailRequest extends FormRequest
{
    public function authorize(): bool
    {
        return !auth()->guest();
    }

    public function rules(): array
    {
        return [
            'token' => ['required', 'string'],
            'expired_at' => ['required', 'string']
        ];
    }
}
