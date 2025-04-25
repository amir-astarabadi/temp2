<?php

namespace App\Http\Requests\Dataset;

use Illuminate\Foundation\Http\FormRequest;

class DatasetUpdateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ];
    }
}
