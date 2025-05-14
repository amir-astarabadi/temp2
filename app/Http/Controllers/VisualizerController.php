<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chart\ChartUpdateLayoutRequest;
use App\Http\Requests\Chart\ChartCreateRequest;
use App\Http\Resources\Chart\ChartResource;
use App\Models\Chart;
use Illuminate\Http\Response as HttpResponse;
use App\Responses\Response;
use App\Models\Dataset;

class VisualizerController extends Controller
{
    public function show(Dataset $dataset)
    {
        abort_if($dataset->user_id !== auth()->id(), HttpResponse::HTTP_UNAUTHORIZED, 'This visualizer does not belong to you!');

        $layouts = $dataset->charts()
            ->select([
                'id',
                'title',
                'description',
                'variables',
                'chart_layout',
                'dataset_id',
                'chart_type'
            ])->get()
            ->toArray();

        return Response::success(message: "visualizer layout retrieved successfully.", data: $layouts);
    }

    public function updateLayout(ChartUpdateLayoutRequest $request)
    {
        $chartType = $request->validated('chart_type');

        if (!method_exists($this->chartService, $chartType)) {
            return Response::error("Sorry, chart type '$chartType' does not support yet.", HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metadata = $this->chartService->{$chartType}($dataset->id, $request->validated('variables'), $request->validated('category_variable'));

        $chartData = array_merge($request->validated(), ['metadata' => $metadata]);
        $chart = $this->chartService->save($chartData);
        return Response::success(message: "", data: $chart->metadata);
    }
}
