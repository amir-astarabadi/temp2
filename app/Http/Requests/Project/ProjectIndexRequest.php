<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectIndexRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'query' => 'string|max:255',
        ];
    }
}
