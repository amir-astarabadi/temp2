<?php

namespace App\Http\Controllers;

use App\Http\Requests\Visualizer\VisualizerUpdateRequest;
use App\Services\Visualizer\VisualizerService;
use Illuminate\Http\Response as HttpResponse;
use App\Responses\Response;
use App\Models\Dataset;

class VisualizerController extends Controller
{
    public function __construct(private VisualizerService $visualizerService){}

    public function show(Dataset $dataset)
    {
        abort_if($dataset->user_id !== auth()->id(), HttpResponse::HTTP_UNAUTHORIZED, 'This visualizer does not belong to you!');

        $layouts = $this->visualizerService->makeLayout($dataset);

        return Response::success(message: "visualizer layout retrieved successfully.", data: $layouts);
    }

    public function update(VisualizerUpdateRequest $request, Dataset $dataset)
    {
        $this->visualizerService->update($dataset, $request->validated('layout'));

        $layouts = $this->visualizerService->makeLayout($dataset);

        return Response::success(message: "visualizer layout updatede successfully.", data: $layouts);
    }
}
