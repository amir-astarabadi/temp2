<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use App\Models\Project;
use App\Models\User;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Dataset>
 */
class DatasetFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'name' => fake()->word(),
            'description' => fake()->paragraph(),
            'file_path' => Str::random(10),
            'type' => 'excel',
            'user_id' => $user->getKey(),
            'project_id' => Project::factory()->for($user)->create()->getKey(),
            'order' => 1,
            'pinned_at' => null,
        ];
    }
}
