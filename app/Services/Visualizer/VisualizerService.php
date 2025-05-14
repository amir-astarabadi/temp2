<?php

namespace App\Services\Visualizer;

use App\Models\Dataset;
use App\Models\Chart;

class VisualizerService
{

    public function update(Dataset $dataset, array $layout)
    {
        foreach($layout as $chart){
            Chart::where('id', $chart['id'])->update(['chart_layout' => $chart['chart_layout']]);
        }
    }

    public function makeLayout(Dataset $dataset): array
    {
        return $dataset->charts()
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
    }
}
