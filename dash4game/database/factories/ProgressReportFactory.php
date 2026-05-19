<?php

namespace Database\Factories;

use App\Models\Project;
use App\Models\ProgressReport;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<ProgressReport> */
class ProgressReportFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '-1 week');

        return [
            'project_id'       => Project::factory(),
            'created_by'       => User::factory(),
            'title'            => fake()->sentence(4),
            'period_start_date' => $start,
            'period_end_date'  => fake()->dateTimeBetween($start, 'now'),
            'summary'          => fake()->paragraph(),
            'completed_work'   => fake()->paragraph(),
            'current_blockers' => fake()->optional()->sentence(),
            'next_goals'       => fake()->paragraph(),
        ];
    }
}
