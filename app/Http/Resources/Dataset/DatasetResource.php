<?php

namespace App\Http\Resources\Dataset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DatasetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id"=> $this->getKey(),
            "name"=> $this->name,
            "description"=> $this->description,
            "file_path"=> $this->file_url,
            "status"=> $this->status,
            "file_type"=> $this->type,
            "created_at"=> $this->created_at
        ];
    }
}
