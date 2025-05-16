<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chart\ChartCreateRequest;
use App\Http\Requests\Chart\ChartUpdateRequest;
use Illuminate\Http\Response as HttpResponse;
use App\Http\Resources\Chart\ChartResource;
use App\Services\Chart\ChartService;
use App\Responses\Response;
use App\Models\Dataset;
use App\Models\Chart;
use App\Services\Visualizer\VisualizerService;

class ChartController extends Controller
{
    public function __construct(private ChartService $chartService, private VisualizerService $visualizerService) {}

    public function store(ChartCreateRequest $request, Dataset $dataset)
    {
        $chartType = $request->validated('chart_type');

        if (!method_exists($this->chartService, $chartType)) {
            return Response::error("Sorry, chart type '$chartType' does not support yet.", HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metadata = $this->chartService->{$chartType}($dataset->id, $request->validated('variables'));
        $chartData = array_merge($request->validated(), ['metadata' => $metadata]);
        $chart = $this->chartService->save($chartData);
        return Response::success(message: "", data: ChartResource::make($chart));
    }

    public function update(ChartUpdateRequest $request, Chart $chart)
    {
        $chartData = $request->validated();

        if ($request->needRegenerateChart()) {
            $chartType = $request->validated('chart_type');

            if (!method_exists($this->chartService, $chartType)) {
                return Response::error("Sorry, chart type '$chartType' does not support yet.", HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            $metadata = $this->chartService->{$chartType}($chart->dataset_id, $request->validated('variables'));
            $chartData = array_merge($request->validated(), ['metadata' => $metadata]);
        }

        $this->chartService->update($chart, $chartData);

        return Response::success(message: "", data: ChartResource::make($chart));
    }

    public function show(Chart $chart)
    {
        abort_if($chart->dataset->user_id !== auth()->id(), HttpResponse::HTTP_UNAUTHORIZED, 'This chart does not belong to you!');

        return Response::success(message: "", data: ChartResource::make($chart));
    }

    public function destroy(Chart $chart)
    {
        abort_if($chart->dataset->user_id !== auth()->id(), HttpResponse::HTTP_UNAUTHORIZED, 'This chart does not belong to you!');
        
        $dataset = $chart->dataset;

        $chart->delete();

        $layouts = $this->visualizerService->makeLayout($dataset);

        return Response::success(message: "Chart deleted successfully.", data: $layouts);
    }
}
