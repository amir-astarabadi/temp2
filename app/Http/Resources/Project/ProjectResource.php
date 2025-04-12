<?php

namespace App\Http\Resources\Project;

use App\Http\Resources\Dataset\DatasetResource;
use App\Http\Resources\Dataset\DatasetResourceCollection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'datasets' => DatasetResourceCollection::make($this->datasets)
        ];
    }
}
