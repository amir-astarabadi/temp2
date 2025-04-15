<?php

namespace Tests\Feature\Controllers\Authentication;

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
                ->has('data', 2)
                ->has('data.0.id')
                ->has('data.0.name')
                ->has('data.0.description')
                ->has('data.0.created_at')
                ->etc();
        });
    }


    public function test_search_in_projects(): void
    {
        $this->login();

        $projects = Project::factory(2)->for($this->authUser)->create();

        $response = $this->getJson(route('projects.index', ['name' => $projects[0]->name]));

        $response->assertOk();
        $response->assertJson(
            fn(AssertableJson $assertableJson) =>
            $assertableJson
                ->has('data')
                ->has('data', 1)
                ->where('data.0.id', $projects[0]->getKey())
                ->etc()
        );
    }
}
