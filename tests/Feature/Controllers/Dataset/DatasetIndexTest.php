<?php

namespace Tests\Feature\Controllers\Dataset;

use Illuminate\Testing\Fluent\AssertableJson;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Queue;
use App\Models\Dataset;
use Tests\TestCase;

class DatasetIndexTest extends TestCase
{

    public function test_happy_path(): void
    {
        Storage::fake();
        Queue::fake();
        $this->login();
        $datasets = Dataset::factory(2)->for($this->authUser)->create();

        $response = $this->getJson(route('datasets.index', ['query' => $datasets->first()->name]));

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->count('data', 1)
                ->where('data.0.id', $datasets->first()->project_id)
                ->where('data.0.datasets.0.id', $datasets->first()->getKey())
                ->etc()
        );
    }
}
