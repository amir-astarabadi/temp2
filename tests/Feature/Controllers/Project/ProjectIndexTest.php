<?php

namespace Tests\Feature\Controllers\Project;

use App\Models\Project;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class ProjectIndexTest extends TestCase
{

    public function test_happy_path(): void
    {
        $this->login();

        Project::factory(2)->for($this->authUser)->create();

        $response = $this->getJson(route('projects.index'));

        $response->assertOk();
        $response->assertJson(function (AssertableJson $assertableJson) {
            $assertableJson
                ->has('data')
                ->has('data.projects', 2)
                ->has('data.projects.0.id')
                ->has('data.projects.0.name')
                ->has('data.projects.0.description')
                ->has('data.projects.0.created_at')
                ->etc();
        });
    }


    public function test_search_in_projects(): void
    {
        $this->login();

        $projects = Project::factory(2)->for($this->authUser)->create();

        $response = $this->getJson(route('projects.index', ['query' => $projects[0]->name]));

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->count('data.projects', 1)
                ->where('data.projects.0.id', $projects[0]->getKey())
                ->etc()
        );
    }
}
