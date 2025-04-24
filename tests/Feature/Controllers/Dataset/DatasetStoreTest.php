<?php

namespace Tests\Feature\Controllers\Dataset;

use App\Jobs\UploadDatasetToMinio;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DatasetStoreTest extends TestCase
{

    public function test_happy_path(): void
    {
        Storage::fake();
        Queue::fake();
        $this->login();
        $project = Project::factory()->for($this->authUser)->create();
        $datasetData = [
            'name' => 'Test Dataset',
            'description' => 'This is a test dataset.',
            'dataset' => UploadedFile::fake()->create('test.xls', 10),
            'project_id' => $project->id,
        ];

        $response = $this->postJson(route('datasets.store'), $datasetData);

        Queue::assertPushed(UploadDatasetToMinio::class);
        $response->assertCreated();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->has('data.id')
                ->where('data.name', $datasetData['name'])
                ->where('data.description', $datasetData['description'])
                ->where('data.status', 'uploading')
                ->has('data.file_path')
                ->has('data.created_at')
                ->etc()
        );

        $this->assertDatabaseHas('datasets', [
            'name' => $datasetData['name'],
            'description' => $datasetData['description'],
            'user_id' => $this->authUser->getKey(),
            'project_id' => $project->getKey(),
        ]);
    }
}
