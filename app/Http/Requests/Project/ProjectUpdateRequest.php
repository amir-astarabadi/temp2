<?php

namespace App\Http\Requests\Project;

use Illuminate\Foundation\Http\FormRequest;

class ProjectUpdateRequest extends FormRequest
{

    public function rules(): array
    {
        return [
            'name' => ['string', 'max:256'],
            'description' => ['string'],
        ];
    }
}
