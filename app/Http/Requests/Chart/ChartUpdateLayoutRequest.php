<?php

namespace App\Http\Requests\Chart;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Dataset;
use Illuminate\Http\Response;

class ChartUpdateLayoutRequest extends FormRequest
{
    private ?Dataset $dataset = null;

    public function authorize(): bool
    {
        return auth()->id() === $this->dataset?->user_id;
    }

    public function failedAuthorization()
    {
        abort(Response::HTTP_UNAUTHORIZED, 'You Just Can Add Chart To Your Dataset');
    }

    public function rules(): array
    {
        return [
            'charts_position' => ['required', 'array'],
        ];
    }

    public function prepareForValidation()
    {
        $this->dataset = $this->route('dataset');

        return $this->merge([
            'dataset_id' => $this->dataset?->id
        ]);
    }

    public function messages()
    {

        return [
            'name.unique' => 'The dataset name has already been taken for this project.',
        ];
    }
}
