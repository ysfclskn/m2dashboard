<?php

namespace Tests\Feature;

use App\Enums\ProjectRole;
use App\Enums\SprintStatus;
use App\Models\Project;
use App\Models\ProjectMember;
use App\Models\Sprint;
use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RaidSprintsOverviewTest extends TestCase
{
    use RefreshDatabase;

    private function projectWithOwner(): array
    {
        $owner   = User::factory()->create();
        $project = Project::factory()->create(['owner_id' => $owner->id]);
        return [$owner, $project];
    }

    // ── Sprint grouping visibility ─────────────────────────────────────────

    public function test_active_sprint_is_visible_on_index(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        $sprint = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active, 'name' => 'Active Raid Alpha']);

        $this->actingAs($owner)
            ->get(route('projects.sprints.index', $project))
            ->assertOk()
            ->assertSee('Active Raid Alpha');
    }

    public function test_planned_sprint_is_visible_on_index(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Planned, 'name' => 'Planned Raid Beta']);

        $this->actingAs($owner)
            ->get(route('projects.sprints.index', $project))
            ->assertOk()
            ->assertSee('Planned Raid Beta');
    }

    public function test_completed_sprint_is_visible_on_index(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Completed, 'name' => 'Done Raid Gamma']);

        $this->actingAs($owner)
            ->get(route('projects.sprints.index', $project))
            ->assertOk()
            ->assertSee('Done Raid Gamma');
    }

    // ── Quest list under sprint ────────────────────────────────────────────

    public function test_quests_under_sprint_are_displayed(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        $sprint = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        $task   = Task::factory()->create(['project_id' => $project->id, 'sprint_id' => $sprint->id, 'created_by' => $owner->id]);

        $this->actingAs($owner)
            ->get(route('projects.sprints.index', $project))
            ->assertOk()
            ->assertSee($task->title);
    }

    public function test_quest_status_is_shown_under_sprint(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        $sprint = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);
        Task::factory()->create(['project_id' => $project->id, 'sprint_id' => $sprint->id, 'created_by' => $owner->id, 'status' => \App\Enums\TaskStatus::Done]);

        $this->actingAs($owner)
            ->get(route('projects.sprints.index', $project))
            ->assertOk()
            ->assertSee('Completed'); // TaskStatus::Done->label()
    }

    // ── Authorization ──────────────────────────────────────────────────────

    public function test_unauthenticated_user_cannot_view_sprints(): void
    {
        $project = Project::factory()->create();

        $this->get(route('projects.sprints.index', $project))
            ->assertRedirect(route('login'));
    }

    public function test_non_member_cannot_view_sprints(): void
    {
        $project  = Project::factory()->create();
        $outsider = User::factory()->create();

        $this->actingAs($outsider)
            ->get(route('projects.sprints.index', $project))
            ->assertForbidden();
    }

    public function test_viewer_can_view_sprint_overview(): void
    {
        [$owner, $project] = $this->projectWithOwner();
        $viewer = User::factory()->create();
        ProjectMember::create(['project_id' => $project->id, 'user_id' => $viewer->id, 'role' => ProjectRole::Viewer]);

        $this->actingAs($viewer)
            ->get(route('projects.sprints.index', $project))
            ->assertOk();
    }

    // ── One active sprint rule ─────────────────────────────────────────────

    public function test_creating_active_sprint_deactivates_existing_active(): void
    {
        [$owner, $project] = $this->projectWithOwner();

        $existing = Sprint::factory()->create(['project_id' => $project->id, 'status' => SprintStatus::Active]);

        $this->actingAs($owner)->post(route('projects.sprints.store', $project), [
            'name'   => 'New Active Raid',
            'status' => 'active',
            'goal'   => null,
            'start_date' => null,
            'end_date'   => null,
        ]);

        $this->assertDatabaseHas('sprints', ['id' => $existing->id, 'status' => 'planned']);
        $this->assertDatabaseHas('sprints', ['name' => 'New Active Raid', 'status' => 'active']);
    }
}
