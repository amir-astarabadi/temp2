<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectCreateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'owner_id' => 'required'
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'owner_id' => $this->user()->getKey(),
        ]);
    }
}
