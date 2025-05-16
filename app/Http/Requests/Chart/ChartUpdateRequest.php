<?php

namespace App\Http\Requests\Chart;

use Illuminate\Foundation\Http\FormRequest;
use App\Rules\ChartVariablesValidation;
use Illuminate\Http\Response;
use App\Models\Chart;

class ChartUpdateRequest extends FormRequest
{
    private bool $needRegenerate = false;

    public function authorize(): bool
    {
        return auth()->id() === $this->route('chart')?->dataset?->user_id;
    }

    public function failedAuthorization()
    {
        abort(Response::HTTP_UNAUTHORIZED, 'This chart does not belongs to you.');
    }

    public function rules(): array
    {
        $baseRules = [
            'title' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
        ];
        $typeAndVariableRules = [];

        if($this->hasChartTypeChanged() || $this->hasVariablesChanged()){
            $typeAndVariableRules = $this->gettypeAndVariableRules();
            $this->needRegenerate = true;
        }

        return array_merge($baseRules, $typeAndVariableRules);
    }

    public function messages()
    {
        return [
            'name.unique' => 'The dataset name has already been taken for this project.',
        ];
    }

    private function gettypeAndVariableRules(): array
    {
        return [
            'chart_type' => ['required', 'string', 'in:' . Chart::getTypes()],
            'variables' => ['required', 'array', new ChartVariablesValidation($this->route('chart')->dataset, $this->get('chart_type'))],
        ];
    }

    private function hasChartTypeChanged()
    {
        return $this->get('chart_type') !== $this->route('chart')->chart_type;
    }

    private function hasVariablesChanged()
    {
        $oldVariables = $this->route('chart')->variables;

        foreach ($this->get('variables') as $key => $value) {
            if (!isset($oldVariables[$key]) || $oldVariables[$key] !== $value) {
                return true;
            }
        }

        return false;
    }

    public function needRegenerateChart(): bool
    {
        return $this->needRegenerate;
    }
}
