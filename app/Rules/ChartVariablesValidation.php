<?php

namespace App\Rules;

use App\Models\Chart;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Arr;
use App\Models\Dataset;
use Closure;

class ChartVariablesValidation implements ValidationRule
{
    public function __construct(private Dataset $dataset, private string $chartTrype) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $chartTrypeValidation = $this->chartTrype . "ChartValidation";
        if (method_exists($this, $chartTrypeValidation)) {

            if ($message = $this->{$chartTrypeValidation}($value)) {
                $fail($message);
            }
            return;
        }

        $fail("Invalid Chart Type $this->chartTrype. we support " . Chart::getTypes());
    }

    private function lineChartValidation(array $columns, $chartType = 'line chart'): null|string
    {
        $requestedVariableKinds = array_keys($columns);
        $validVariableKinds = ["independent_variable", "dependent_variable"];
        if (array_diff($validVariableKinds, $requestedVariableKinds)) {
            return "for $chartType you should select 'independent' and 'dependent' variables.";
        }

        $variables = Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::except($columns, 'category_variable')));
        if (count($variables) !== 2) {
            return "for $chartType at least two variables must select.";
        }

        $categoryVariable = array_values(Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::only($columns, 'category_variable'))));

        if ($categoryVariable && $categoryVariable[0]['type'] !== 'categorical') {
            return "for $chartType 'category variable' must have 'categorical' type.";
        }

        foreach ($variables as $variable) {
            if ($variable['type'] !== "numeric") {
                return "$chartType support only 'numeric' variables(" . $variable['column'] . " is not numeric)";
            }
        }

        return null;
    }

    private function scatterChartValidation($columns): null|string
    {
        return $this->lineChartValidation($columns, 'scatter plot');
    }

    private function histogramChartValidation(array $columns): null|string
    {
        $requestedVariableKinds = array_keys($columns);
        $validVariableKinds = ["independent_variable"];
        if (array_diff($validVariableKinds, $requestedVariableKinds)) {
            return "for histogram you should select 'independent' variables.";
        }

        $variables = array_values(Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::only($columns, 'independent_variable'))));
        if (count($variables) !== 1 || $variables[0]['type'] !== 'numeric') {
            return "for histogram chart one continues variable must select.";
        }

        $categoryVariable = array_values(Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::only($columns, 'category_variable'))));
        if (count($categoryVariable) && $categoryVariable[0]['type'] !== 'categorical') {
            return "for histogram chart 'category variable' must be categorical.";
        }

        $static = $columns['statistic'] ?? null;
        if (!in_array($static, ['frequency', 'percent', 'density'])) {
            return "for histogram chart statistic can be 'frequency', 'percent', 'density'.";
        }

        return null;
    }

    private function barChartValidation(array $columns): null|string
    {
        $requestedVariableKinds = array_keys($columns);
        $validVariableKinds = ["independent_variable"];
        if (array_diff($validVariableKinds, $requestedVariableKinds)) {
            return "for bar chart you should select 'independent' variable.";
        }

        $variable = Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::only($columns, 'independent_variable')));
        if (count($variable) !== 1) {
            return "selected 'independent' variable does not belongs to this dataset.";
        }

        if (isset($columns['category_variable'])) {
            $categoryVariable = Arr::first($this->dataset->metadata, fn($record) => $record['column'] == $columns['category_variable']);
            if (is_null($categoryVariable)) {
                return "selected 'cagegory' variable does not belongs to this dataset.";
            }
            if ($categoryVariable['type'] && $categoryVariable['type'] !== 'categorical') {
                return "for bar chart 'category variable' must be categorical.";
            }
        }

        return null;
    }
}
