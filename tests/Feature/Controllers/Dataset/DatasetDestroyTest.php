<?php

namespace Tests\Feature\Controllers\Dataset;

use App\Jobs\DeleteDatasetFromMinio;
use App\Jobs\UploadDatasetToMinio;
use App\Models\Dataset;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class DatasetDestroyTest extends TestCase
{

    public function test_happy_path(): void
    {
        Storage::fake();
        Queue::fake();
        $this->login();
        $dataset = Dataset::factory()->for($this->authUser)->create();

        $response = $this->deleteJson(route('datasets.destroy', ['dataset' => $dataset]));

        Queue::assertPushed(DeleteDatasetFromMinio::class);
        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->where('message', 'Dataset deleted successfully.')
                ->etc()
        );

        $this->assertSoftDeleted('datasets', ['id' => $dataset->getKey()]);
    }
}
