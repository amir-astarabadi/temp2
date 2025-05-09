<?php

namespace App\Http\Requests\Dataset;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class DatasetCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->projects()->whereId($this->get('project_id'))->exists();
    }

    public function failedAuthorization()
    {
        abort(Response::HTTP_UNAUTHORIZED, 'you can not upload dataset on projects which not belongs to you.');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('datasets')->where(function ($query) {
                return $query
                    ->where('user_id', $this->user()->getKey())
                    ->where('project_id', $this->input('project_id'))
                    ->whereNull('deleted_at');
            })],
            'description' => 'nullable|string',
            'dataset' => 'required|file|mimetypes:text/plain,text/csv,application/csv,application/vnd.ms-excel,text/comma-separated-values,application/vnd.openxmlformats-officedocument.spreadsheetml.sheet|max:102400',
            'project_id' => 'required|exists:projects,id',
        ];
    }

    public function messages()
    {
        return [
            'name.unique' => 'The dataset name has already been taken for this project.',
        ];
    }
}
