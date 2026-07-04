<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Models\WikiPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuestWikiLinkTest extends TestCase
{
    use RefreshDatabase;

    private function setupProject(): array
    {
        $owner = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        $wikiPage = WikiPage::factory()->create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'title' => 'My Combat Guide'
        ]);
        return [$owner, $project, $wikiPage];
    }

    public function test_can_create_quest_linked_to_wiki_page(): void
    {
        [$owner, $project, $wikiPage] = $this->setupProject();

        $response = $this->actingAs($owner)
            ->post(route('projects.quests.store', $project), [
                'title' => 'Test Quest With Wiki',
                'description' => 'Detailed markdown description here.',
                'type' => 'feature',
                'status' => 'todo',
                'priority' => 'high',
                'wiki_page_id' => $wikiPage->id,
            ]);

        $response->assertRedirect(route('projects.quests.index', $project));
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Quest With Wiki',
            'wiki_page_id' => $wikiPage->id,
        ]);
    }

    public function test_can_update_quest_linked_to_wiki_page(): void
    {
        [$owner, $project, $wikiPage] = $this->setupProject();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'title' => 'Existing Quest'
        ]);

        $response = $this->actingAs($owner)
            ->put(route('projects.quests.update', [$project, $task]), [
                'title' => 'Updated Quest Title',
                'description' => 'Updated markdown description.',
                'type' => $task->type->value,
                'status' => $task->status->value,
                'priority' => $task->priority->value,
                'wiki_page_id' => $wikiPage->id,
            ]);

        $response->assertRedirect(route('projects.quests.show', [$project, $task]));
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Quest Title',
            'wiki_page_id' => $wikiPage->id,
        ]);
    }

    public function test_displays_wiki_link_on_quest_detail_page(): void
    {
        [$owner, $project, $wikiPage] = $this->setupProject();
        $task = Task::factory()->create([
            'project_id' => $project->id,
            'created_by' => $owner->id,
            'wiki_page_id' => $wikiPage->id,
            'description' => 'Implement **markdown** logic.'
        ]);

        $response = $this->actingAs($owner)
            ->get(route('projects.quests.show', [$project, $task]));

        $response->assertOk()
            ->assertSee('My Combat Guide')
            ->assertSee('task-description')
            ->assertSee('Implement **markdown** logic.');
    }
}
