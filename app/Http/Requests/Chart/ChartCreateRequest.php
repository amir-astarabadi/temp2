<?php

namespace App\Http\Requests\Chart;

use App\Models\Chart;
use App\Models\Dataset;
use App\Rules\ChartVariablesValidation;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use Illuminate\Validation\Rule;

class ChartCreateRequest extends FormRequest
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
            'title' => ['required', 'string'],
            'chart_type' => ['required', 'string', 'in:' . Chart::getTypes()],
            'description' => 'nullable|string',
            'dataset_id' => ['required', 'integer'],
            'category_variable' => ['nullable', 'string', "in:" . implode(',', $this->dataset->categorical_columns)],
            'variables' => [Rule::requiredIf($this->get('chart_type') == 'line'), 'array', new ChartVariablesValidation($this->dataset, $this->get('chart_type'))],
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
