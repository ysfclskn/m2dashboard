<?php

namespace Database\Factories;

use App\Enums\ProjectStatus;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<Project> */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'owner_id'    => User::factory(),
            'name'        => ucwords($name),
            'slug'        => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 9999),
            'description' => fake()->paragraph(),
            'genre'       => fake()->randomElement(['RPG', 'Action', 'Puzzle', 'Strategy', 'Adventure', 'Platformer']),
            'status'      => fake()->randomElement(ProjectStatus::cases()),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => ProjectStatus::Production]);
    }
}
