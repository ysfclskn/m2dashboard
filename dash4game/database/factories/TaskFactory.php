<?php

namespace Database\Factories;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Enums\TaskType;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<Task> */
class TaskFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id'  => Project::factory(),
            'sprint_id'   => null,
            'assignee_id' => null,
            'created_by'  => User::factory(),
            'title'       => fake()->sentence(5),
            'description' => fake()->paragraph(),
            'type'        => fake()->randomElement(TaskType::cases()),
            'status'      => fake()->randomElement(TaskStatus::cases()),
            'priority'    => fake()->randomElement(TaskPriority::cases()),
            'order_index' => fake()->numberBetween(0, 50),
            'due_date'    => fake()->optional()->dateTimeBetween('now', '+2 months'),
        ];
    }

    public function done(): static
    {
        return $this->state(['status' => TaskStatus::Done]);
    }

    public function backlog(): static
    {
        return $this->state(['status' => TaskStatus::Backlog, 'sprint_id' => null]);
    }
}
