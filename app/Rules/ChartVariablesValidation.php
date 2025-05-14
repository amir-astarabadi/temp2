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

    public function lineChartValidation(array $columns): null|string
    {
        $requestedVariableKinds = array_keys($columns);
        $validVariableKinds = ["independent_variable", "dependent_variable"];
        if (array_diff($validVariableKinds, $requestedVariableKinds)) {
            return "for line chart you should select 'independent' and 'dependent' variables.";
        }

        $variables = Arr::where($this->dataset->metadata, fn($record) => in_array($record['column'], Arr::except($columns, 'category_variable')));
        if (count($variables) !== 2) {
            return "for line chart at least two variables must select.";
        }

        foreach ($variables as $variable) {
            if ($variable['type'] !== "numeric") {
                return "line chart support only 'numeric' variables(" . $variable['column'] . " is not numeric)";
            }
        }

        return null;
    }

    public function histogramChartValidation(array $columns): null|string
    {
        // TODO : add his validation
        return "add his validation";
    }
}
