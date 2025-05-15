<?php

namespace App\Http\Resources\Chart;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
                "id" => $this->id,
                "dataset_id" => $this->dataset_id,
                "title" => $this->title,
                "chart_type" => $this->chart_type,
                "variables" => $this->variables,
                "description" => $this->description,
                "metadata" => $this->metadata,
                "chart_layout" => $this->chart_layout,
                "created_at" => $this->created_at,
                "updated_at" => $this->updated_at,
            ];
    }
}
