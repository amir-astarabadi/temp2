<?php

namespace Tests\Feature\Controllers\Dataset;

use Illuminate\Testing\Fluent\AssertableJson;
use App\Models\Dataset;
use Tests\TestCase;

class DatasetUpdateTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();
     
        $requestData = [
            'name' => 'Updated Dataset Name',
            'description' => 'Updated Dataset Description',
        ];
        $dataset = Dataset::factory()->for($this->authUser)->create();

        $response = $this->putJson(route('datasets.update', ['dataset' => $dataset->getKey()]), $requestData);

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
            ->where('message', 'Dataset updated successfully.')
                ->has('data')
                ->where('data.id', $dataset->getKey())
                ->where('data.name', $requestData['name'])
                ->where('data.description', $requestData['description'])
                ->etc()
        );
    }
}
