<?php

namespace App\Services\Chart;

use App\Services\Dataset\DatasetService;
use Illuminate\Support\Facades\Http;
use App\Models\Chart;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;

class ChartService
{
    public function __construct(private DatasetService $datasetService) {}

    public function line(int $datasetId, array $variables): array
    {
        $url = config('analyser.line_chart') . $datasetId . "?" . http_build_query($variables);
        $response = Http::withHeaders(['Accept' => 'application/json'])->get($url);
        return $response->json();
    }

    public function scatter(int $datasetId, array $variables): array
    {
        $url = config('analyser.scatter_chart') . $datasetId . "?" . http_build_query($variables);
        $response = Http::withHeaders(['Accept' => 'application/json'])->get($url);
        return $response->json();
    }

    public function histogram(int $datasetId, array $variables): array|Response
    {
        $url = config('analyser.histogram_chart') . $datasetId . "?" . http_build_query($variables);
        
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

    public function update(Chart $chart, array $chartData): void
    {
        foreach ($chartData as $property => $value) {
            $chart->{$property} = $value;
        }
        
        $chart->save();
    }
}
