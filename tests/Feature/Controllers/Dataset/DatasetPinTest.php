<?php

namespace Tests\Feature\Controllers\Dataset;

use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Dataset;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatasetPinTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();


        $notPinDataset = Dataset::factory()->for($this->authUser)->create(['order' => 1]);
        $pinDataset = Dataset::factory()->for($this->authUser)->for($notPinDataset->project)->create(['order' => 2]);
        
        
        $response = $this->putJson(route('datasets.pin', ['dataset' => $pinDataset->getKey()]));
        
        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->where('message', 'Dataset pinned successfully.')
                ->has('data')
                ->where('data.id', $pinDataset->getKey())
                ->where('data.is_pinned', true)
                ->where('data.order', 1)
                ->etc()
        );

        $this->assertDatabaseHas('datasets', [
            'id' => $notPinDataset->getKey(),
            'pinned_at' => null,
            'order' => 2,
        ]);
    }
}
