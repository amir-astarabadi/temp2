<?php

namespace App\Services\Chart;

use Illuminate\Support\Facades\Http;
use App\Models\Chart;

class ChartService
{
    public function line(int $datasetId, array $variables, null|string $categoryVariable = null): array
    {
        $url = config('analyser.line_chart') . $datasetId . "?" . http_build_query($variables);
        $response = Http::withHeaders(['Accept' => 'application/json'])->get($url);
        return $response->json();
    }

    public function save(array $chartData): Chart
    {
        $chart = new Chart();

        foreach ($chartData as $property => $value) {
            $chart->{$property} = $value;
        }

        $chart->save();

        return $chart;
    }
}
