<?php

namespace Database\Factories;

use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Sprint> */
class SprintFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-2 months', 'now');

        return [
            'project_id' => Project::factory(),
            'name'       => 'Raid ' . fake()->numberBetween(1, 20),
            'goal'       => fake()->sentence(),
            'status'     => fake()->randomElement(SprintStatus::cases()),
            'start_date' => $start,
            'end_date'   => fake()->dateTimeBetween($start, '+3 weeks'),
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => SprintStatus::Active]);
    }
}
