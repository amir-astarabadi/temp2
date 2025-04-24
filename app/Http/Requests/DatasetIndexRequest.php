<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DatasetIndexRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'query' => 'string',
        ];
    }
}
