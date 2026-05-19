<?php

namespace Database\Factories;

use App\Enums\WikiPageCategory;
use App\Models\Project;
use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<WikiPage> */
class WikiPageFactory extends Factory
{
    public function definition(): array
    {
        $title = fake()->words(4, true);

        return [
            'project_id' => Project::factory(),
            'created_by' => User::factory(),
            'updated_by' => null,
            'title'      => ucwords($title),
            'slug'       => Str::slug($title),
            'content'    => fake()->paragraphs(3, true),
            'category'   => fake()->randomElement(WikiPageCategory::cases()),
        ];
    }
}
