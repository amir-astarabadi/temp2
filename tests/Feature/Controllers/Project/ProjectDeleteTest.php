<?php

namespace Tests\Feature\Controllers\Authentication;

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectDeleteTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();
        $project = Project::factory()->for($this->authUser)->create();

        $response = $this->deleteJson(route('projects.delete',['project' => $project->getKey()]));

        $response->assertOk();
        $response->assertJson(function (AssertableJson $assertableJson){
            $assertableJson
                ->where('message', 'Project deleted successfully.')
                ->etc();
        });

        $this->assertSoftDeleted($project);
    }
}
