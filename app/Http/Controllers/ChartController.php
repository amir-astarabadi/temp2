<?php

namespace App\Http\Controllers;

use App\Http\Requests\Chart\ChartUpdateLayoutRequest;
use App\Http\Requests\Chart\ChartCreateRequest;
use App\Http\Resources\Chart\ChartResource;
use Illuminate\Http\Response as HttpResponse;
use App\Services\Chart\ChartService;
use App\Responses\Response;
use App\Models\Dataset;

class ChartController extends Controller
{
    public function __construct(private ChartService $chartService) {}

    public function store(ChartCreateRequest $request, Dataset $dataset)
    {
        $chartType = $request->validated('chart_type');
        
        if(!method_exists($this->chartService, $chartType)){
            return Response::error("Sorry, chart type '$chartType' does not support yet.", HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metadata = $this->chartService->{$chartType}($dataset->id, $request->validated('variables'), $request->validated('category_variable'));
        $chartData = array_merge($request->validated(), ['metadata' => $metadata]);
        $chart = $this->chartService->save($chartData);
        return Response::success(message: "",data: ChartResource::make($chart));
    }

    public function updateLayout(ChartUpdateLayoutRequest $request)
    {
        $chartType = $request->validated('chart_type');
        
        if(!method_exists($this->chartService, $chartType)){
            return Response::error("Sorry, chart type '$chartType' does not support yet.", HttpResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $metadata = $this->chartService->{$chartType}($dataset->id, $request->validated('variables'), $request->validated('category_variable'));

        $chartData = array_merge($request->validated(), ['metadata' => $metadata]);
        $chart = $this->chartService->save($chartData);
        return Response::success(message: "",data: $chart->metadata);
    }

}
