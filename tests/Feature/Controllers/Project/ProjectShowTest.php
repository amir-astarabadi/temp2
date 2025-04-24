<?php

namespace Tests\Feature\Controllers\Project;

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectShowTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();
        $project = Project::factory()->for($this->authUser)->create();

        $response = $this->getJson(route('projects.show', ['project' => $project->getKey()]));

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->where('message', 'Project retrieved successfully.')
                ->where('data.name', $project->name)
                ->where('data.description', $project->description)
                ->etc()
        );
    }
}
