<?php

namespace App\Http\Requests\Dataset;

use Illuminate\Foundation\Http\FormRequest;

class DatasetCreateRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'dataset' => 'required|file|mimes:xlsx,xls,csv|max:102400',
            'project_id' => 'required|exists:projects,id',
        ];
    }
}
