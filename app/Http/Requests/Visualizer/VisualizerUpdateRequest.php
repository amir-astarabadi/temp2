<?php

namespace App\Http\Requests\Visualizer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Response;
use App\Models\Chart;

class VisualizerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        $chartsCount = Chart::whereDatasetId($this->route('dataset')?->id)
            ->whereIn('id', array_column($this->get('layout', []), 'id'))
            ->count() === count($this->get('layout', []));
        return auth()->id() === $this->route('dataset')?->user_id && $chartsCount;
    }

    public function failedAuthorization()
    {
        abort(Response::HTTP_UNAUTHORIZED, 'Visualizer does not belong to you.');
    }

    public function rules(): array
    {
        return [
            'layout' => ['required', 'array'],
            'layout.*.id' => ['required', 'integer'],
            'layout.*.chart_layout' => ['required', 'array'],
        ];
    }
}
