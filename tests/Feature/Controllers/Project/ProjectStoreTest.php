<?php

namespace Tests\Feature\Controllers\Authentication;

use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectStoreTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();

        $projectData = [
            'name' => 'Test Project',
            'description' => 'This is a test project.',
        ];

        $response = $this->postJson(route('projects.store'), $projectData);

        $response->assertCreated();
        $response->assertJson(function (AssertableJson $assertableJson) use ($projectData) {
            $assertableJson
                ->has('data')
                ->has('data.id')
                ->where('data.name', $projectData['name'])
                ->where('data.description', $projectData['description'])
                ->has('data.created_at')
                ->etc();
        });

        $this->assertDatabaseHas('projects', [
            'name' => $projectData['name'],
            'description' => $projectData['description'],
            'owner_id' => $this->authUser->getKey(),
        ]);
    }
}
