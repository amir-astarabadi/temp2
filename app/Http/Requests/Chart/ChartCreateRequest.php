<?php

namespace App\Http\Requests\Chart;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChartVariablesValidation;
use Illuminate\Validation\Rule;
use Illuminate\Http\Response;
use App\Models\Dataset;
use App\Models\Chart;

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
            'variables' => [Rule::requiredIf($this->get('chart_type') == 'line'), 'array', new ChartVariablesValidation($this->dataset, $this->get('chart_type'))],
            'chart_layout' => ['required', 'array'],
        ];
    }

    public function prepareForValidation()
    {
        $this->dataset = $this->route('dataset');

        return $this->merge([
            'dataset_id' => $this->dataset?->id
        ]);
    }
}
