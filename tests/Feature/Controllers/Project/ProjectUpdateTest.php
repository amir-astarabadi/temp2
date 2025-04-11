<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectUpdateTest extends TestCase
{
    public function test_happy_path(): void
    {
        $this->login();
        $project = Project::factory()->for($this->authUser)->create();

        $projectData = [
            'name' => 'New Name',
            'description' => 'New Description.',
        ];

        $response = $this->putJson(route('projects.update', ['project' => $project]), $projectData);

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->has('data.id')
                ->where('data.name', $projectData['name'])
                ->where('data.description', $projectData['description'])
                ->has('data.created_at')
                ->etc()
        );


        $this->assertDatabaseHas('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
        ]);
    }
}
